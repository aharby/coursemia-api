<?php

namespace App\OurEdu\Exams\UseCases\PrepareExamQuestion;

use App\OurEdu\Exams\Enums\AptitudeEnums;
use App\OurEdu\Exams\Enums\ExamTypes;
use App\Exceptions\ErrorResponseException;
use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Exams\Models\PrepareExamQuestion;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use App\OurEdu\Subjects\Repository\SubjectFormatSubjectRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use App\OurEdu\Exams\Repository\Exam\ExamRepository;
use App\OurEdu\Options\Repository\OptionRepositoryInterface;
use App\OurEdu\Exams\Repository\Exam\ExamRepositoryInterface;
use App\OurEdu\LearningResources\Enums\DifficultlyLevelEnums;
use App\OurEdu\Exams\Repository\ExamQuestion\ExamQuestionRepositoryInterface;
use App\OurEdu\Exams\UseCases\PrepareExamQuestion\GenerateExamUseCase\GenerateExamUseCase;
use App\OurEdu\Exams\Repository\PrepareExamQuestion\PrepareExamQuestionRepositoryInterface;
use App\OurEdu\Exams\UseCases\PrepareExamQuestion\GenerateExamUseCase\GenerateExamUseCaseInterface;
use Illuminate\Support\Arr;

class GenerateExamUseCaseTwo extends GenerateExamUseCase implements GenerateExamUseCaseInterface
{
    protected ExamRepositoryInterface $examRepository;
    protected PrepareExamQuestionRepositoryInterface $prepareExamQuestionRepository;
    protected OptionRepositoryInterface $optionRepository;
    protected ExamQuestionRepositoryInterface $examQuestionRepository;

    /**
     * Required questions count in each field
     * @var collection
     */
    protected $questionRequirements;

    /**
     * Temporary subject format questions
     * @var collection
     */
    protected $subjectFormatQuestions;

    /**
     * Student already take questions
     * @var collection
     */
    protected $alreadyTakenQuestions;

    /**
     * All the prepared Questions
     * @var collection
     */
    protected $preparedQuestions;

    /**
     * Accepted prepared Questions
     * @var collection
     */
    protected $acceptedQuestions;

    public function __construct(
        ExamRepositoryInterface $examRepository,
        PrepareExamQuestionRepositoryInterface $prepareExamQuestionRepository,
        OptionRepositoryInterface $optionRepository,
        ExamQuestionRepositoryInterface $examQuestionRepository,
        SubjectFormatSubjectRepositoryInterface $subjectFormatSubjectRepository
    ) {
        parent::__construct(
            $examRepository,
            $prepareExamQuestionRepository,
            $optionRepository,
            $subjectFormatSubjectRepository
        );
        $this->examRepository = $examRepository;
        $this->prepareExamQuestionRepository = $prepareExamQuestionRepository;
        $this->optionRepository = $optionRepository;
        $this->examQuestionRepository = $examQuestionRepository;
        $this->acceptedQuestions = collect([]);
        $this->subjectFormatQuestions = collect([]);
    }


    /**
     * @throws ErrorResponseException
     */
    public function generateExam(
        $student,
        $subjectId,
        $sectionIds,
        $numberOfQuestions,
        $difficultyLevelId,
        $allSessionID = null
    ) {
        // complete aptitude test
        if ($this->checkCompleteAptitudeTest($sectionIds)) {
            // todo: reduce code duplication to one function called twice
            $quantitativeId = SubjectFormatSubject::where('slug', AptitudeEnums::QUANTITATIVE_SECTION)->first()->id;
            $quantitativeSections = getSectionsOfSections([$quantitativeId]);
            $quantitativeSections = array_diff($quantitativeSections, [$quantitativeId]);

            $quantitativePreparedQuestions = $this->prepareExamQuestionRepository->getAptitudeTestQuestions(
                $quantitativeSections
            );
            $quantitativePreparedQuestionsCount = $quantitativePreparedQuestions->count();

            if ($quantitativePreparedQuestionsCount < 60) {
                throw new ErrorResponseException(trans('api.number of question less than expected!'));
            } elseif ($quantitativePreparedQuestionsCount > 60) {
                $quantitativePreparedQuestions = $quantitativePreparedQuestions->slice(0, 60);
            }

            $quantitativeChunks = $quantitativePreparedQuestions->chunk(20);

            $verbalId = SubjectFormatSubject::where('slug', AptitudeEnums::VERBAL_SECTION)->first()->id;
            $verbalSections = getSectionsOfSections([$verbalId]);
            $verbalSections = array_diff($verbalSections, [$verbalId]);

            $verbalPreparedQuestions = $this->prepareExamQuestionRepository->getAptitudeTestQuestions($verbalSections);
            $verbalPreparedQuestionsCount = $verbalPreparedQuestions->count();

            if ($verbalPreparedQuestionsCount < 60) {
                throw new ErrorResponseException(trans('api.number of question less than expected!'));
            } elseif ($verbalPreparedQuestionsCount > 60) {
                $verbalPreparedQuestions = $verbalPreparedQuestions->slice(0, 60);
            }

            $verbalChunks = $verbalPreparedQuestions->chunk(20);

            $aptitudeQuestionsSorted = collect([]);

            for ($i = 0; $i < $quantitativeChunks->count(); $i++) {
                $aptitudeQuestionsSorted = $aptitudeQuestionsSorted->merge($quantitativeChunks[$i]);
                $aptitudeQuestionsSorted = $aptitudeQuestionsSorted->merge($verbalChunks[$i]);
            }

            $this->acceptedQuestions = $aptitudeQuestionsSorted;
            return $this->createExamAndAssignQuestionsAptitude($student, $subjectId, $sectionIds);
        }

        $sections = getSectionsOfSections($sectionIds);

        //mark sections progress
        markSectionProgress($sections, $subjectId, $student);

        $difficultyLevel = $this->optionRepository->find($difficultyLevelId)->slug ?? '';

        $validAmount = $this->prepareRequiredQuestionsAmount(
            $student->id,
            $sections,
            $numberOfQuestions,
            $difficultyLevel
        );
        if (isset($validAmount['error'])) {
            return $validAmount;
        }

        // get already answered questions
        $this->alreadyTakenQuestions = $this->examQuestionRepository
                ->getStudentTakenQuestions($student->id, $subjectId, $sections)
                ?->pluck('question_table')->unique('id') ?? collect([]);

        // get subject formats all prepared questions
        $this->preparedQuestions = $this->prepareExamQuestionRepository->getBySubjectFormats($sections) ?? collect([]);

        // case not enough questions
        if ($numberOfQuestions > $this->preparedQuestions->count()) {
            // throw exception
            throw new ErrorResponseException(
                trans(
                    'api.not_enough_questions_change_difficulty_level_or_number_of_questions'
                )
            );
        }

        $questionsPreFilter = $this->preparedQuestions;

        // filter prepared questions based on allowed difficulty levels
        $this->difficultyFilter($difficultyLevel);

        // if required number of questions became > prepared question after applying difficulty filter
        // and before applying it was suitable, then rollback that filter
        if ($numberOfQuestions > $this->preparedQuestions->count()) {
            $this->preparedQuestions = $questionsPreFilter;
        }

        // filter based on already taken questions
        $this->alreadyTakenFilter();

        // take questions based on exam question requirements
        $this->questionRequirementsFilter($difficultyLevel);

        // after all: if the required $numberOfQuestions is bigger than the acceptedQuestions
        if ($numberOfQuestions > $this->acceptedQuestions->count()) {
            // throw exception
            throw new ErrorResponseException(
                trans(
                    'api.not_enough_questions_change_difficulty_level_or_number_of_questions'
                )
            );
        }

        return $this->createExamAndAssignQuestions(
            $student,
            $subjectId,
            $sectionIds,
            $numberOfQuestions,
            $difficultyLevel
        );
    }

    /**
     * Find prepare question count on each subject format
     * and return the counter results to use while choosing questions
     * @param array $sectionIds
     * @param int $numberOfQuestions
     * @param string $difficultyLevel
     * @return array|void
     */
    protected function prepareRequiredQuestionsAmount($studentId, $sectionIds, $numberOfQuestions, $difficultyLevel)
    {
        if (count($sectionIds) > $numberOfQuestions) {
            $sectionIds = Arr::random($sectionIds, $numberOfQuestions);
        }
        $resultCollection = $this->prepareExamQuestionRepository->getSubjectFormatQuestionsCount(
            $sectionIds,
            $numberOfQuestions,
            [$difficultyLevel],
            $studentId,
        );

        if (!count($resultCollection)) {
            return [
                'error' => true,
                'message' => trans('exam.Exam not created, use different criteria'),
            ];
        }

        $resultCollection = $resultCollection->sortBY('return_question_count');

        $this->questionRequirements = $resultCollection;
    }


    /**
     * Filter prepared questions and return allowed levels
     * @param string $difficultyLevel
     * @return void
     * @throws ErrorResponseException
     */
    protected function difficultyFilter(string $difficultyLevel)
    {
        if (!in_array($difficultyLevel, DifficultlyLevelEnums::availableDifficultlyLevel())) {
            throw new ErrorResponseException(trans('app.Unknown difficulty level'));
        }

        // allowed difficulty levels
        $allowedLevels = match ($difficultyLevel) {
            DifficultlyLevelEnums::DIFFICULT => [
                DifficultlyLevelEnums::DIFFICULT,
                DifficultlyLevelEnums::MEDIUM,
                DifficultlyLevelEnums::EASY,
            ],
            DifficultlyLevelEnums::MEDIUM => [
                DifficultlyLevelEnums::MEDIUM,
                DifficultlyLevelEnums::EASY,
            ],
            DifficultlyLevelEnums::EASY => [
                DifficultlyLevelEnums::EASY,
            ]
        };

        $this->preparedQuestions = $this->preparedQuestions->whereIn('difficulty_level', $allowedLevels);
    }

    /**
     * Filter prepared questions based on alreay taken questions
     * @return void
     */
    protected function alreadyTakenFilter()
    {
        $this->preparedQuestions = $this->preparedQuestions->map(function ($preparedQuestion) {
            $this->alreadyTakenQuestions->contains(
                $preparedQuestion->question_table
            ) ? $preparedQuestion->already_taken = 1 : $preparedQuestion->already_taken = 0;

            return $preparedQuestion;
        });
    }

    /**
     * Filter prepared questions based on question requirements
     * @param $difficultyLevel
     * @return void
     */
    protected function questionRequirementsFilter($difficultyLevel)
    {
        $this->questionRequirements->each(function ($requirement) use ($difficultyLevel) {
            $this->addToAcceptedQuestions($requirement, $difficultyLevel);
        });
    }

    /**
     * Add questions to accepted question based on requirements
     * @param collection $requirement
     * @param $difficultyLevel
     * @param void
     */
    protected function addToAcceptedQuestions($requirement, $difficultyLevel)
    {
        $questions = $this->preparedQuestions
            ->where('subject_format_subject_id', $requirement->subject_format_subject_id)
            ->where('difficulty_level', $difficultyLevel)
            ->where('already_taken', 0)
            ->take($requirement->return_question_count);

        // not enough questions then take from taken questions
        if ($requirement->return_question_count > $questions->count()) {
            $takenQuestions = $this->preparedQuestions
                ->where('subject_format_subject_id', $requirement->subject_format_subject_id)
                ->where('difficulty_level', $difficultyLevel)
                ->where('already_taken', 1)
                ->take($requirement->return_question_count - $questions->count());

            $questions = $questions->merge($takenQuestions);
        }

        $this->subjectFormatQuestions = $this->subjectFormatQuestions->merge($questions);

        // case difficult
        if ($difficultyLevel == DifficultlyLevelEnums::DIFFICULT) {
            $this->addToAcceptedQuestions($requirement, DifficultlyLevelEnums::MEDIUM);
        }

        // case medium
        if ($difficultyLevel == DifficultlyLevelEnums::MEDIUM) {
            $this->addToAcceptedQuestions($requirement, DifficultlyLevelEnums::EASY);
        }
        // last round
        if ($difficultyLevel == DifficultlyLevelEnums::EASY) {
            // add the required questions
            $this->acceptedQuestions = $this->acceptedQuestions->merge(
                $this->subjectFormatQuestions->unique()->take($requirement->return_question_count)
            );

            // empty questions to use again
            $this->subjectFormatQuestions = collect([]);
        }
    }

    /**
     * @param $student
     * @param $subjectId
     * @param $sectionIds
     * @param $numberOfQuestions
     * @param $difficultyLevel
     * @return Exam|PrepareExamQuestion|null
     */
    protected function createExamAndAssignQuestions(
        $student,
        $subjectId,
        $sectionIds,
        $numberOfQuestions,
        $difficultyLevel
    ) {
        $title = $this->getExamTitleFromSubjectFormats($sectionIds);
        $exam = $this->examRepository->create([
            'student_id' => $student->id,
            'type' => ExamTypes::EXAM,
            'title' => $title,
            'questions_number' => $numberOfQuestions,
            'difficulty_level' => $difficultyLevel,
            'subject_id' => $subjectId,
            'subject_format_subject_id' => json_encode($sectionIds)

        ]);

        $examRepo = new ExamRepository($exam);

        if ($this->acceptedQuestions->count() > $numberOfQuestions) {
            $this->acceptedQuestions = $this->acceptedQuestions->slice(0, $numberOfQuestions);
        }
        $this->acceptedQuestions->each(function ($question) use ($examRepo, $exam) {
            $timeToSolve = !is_null($question->time_to_solve)
                ? $question->time_to_solve
                : env('exam_default_question_time', 30);

            $data = [
                'slug' => $question->question_type,
                'exam_id' => $exam->id,
                'question_type' => $question->question_type,
                'question_table_type' => $question->question_table_type,
                'question_table_id' => $question->question_table_id,
                'subject_id' => $question->subject_id,
                'subject_format_subject_id' => $question->subject_format_subject_id,
                'time_to_solve' => $timeToSolve,
            ];

            $examRepo->createQuestions($data);
        });

        $sumTime = $examRepo->getSumTimeForAllExamQuestion();

        $examRepo->update($exam, ['time_to_solve' => $sumTime]);

        return $exam;
    }

    /**
     * @param $student
     * @param $subjectId
     * @param $sectionIds
     * @return Exam|PrepareExamQuestion|null
     */
    protected function createExamAndAssignQuestionsAptitude($student, $subjectId, $sectionIds)
    {
        $title = $this->getExamTitleFromSubjectFormats($sectionIds);
        $exam = $this->examRepository->create([
            'student_id' => $student->id,
            'type' => ExamTypes::EXAM,
            'title' => $title,
            'questions_number' => AptitudeEnums::TOTAL_NUMBER_OF_QUESTIONS, // todo ada
            'difficulty_level' => null,
            'subject_id' => $subjectId,
            'subject_format_subject_id' => json_encode($sectionIds)

        ]);

        $examRepo = new ExamRepository($exam);

        $this->acceptedQuestions->each(function ($question) use ($examRepo, $exam) {
            // todo: add it also when sme or content author add data
            $timeToSolve = env('aptitude_default_question_time', 60);

            $data = [
                'slug' => $question->question_type,
                'exam_id' => $exam->id,
                'question_type' => $question->question_type,
                'question_table_type' => $question->question_table_type,
                'question_table_id' => $question->question_table_id,
                'subject_id' => $question->subject_id,
                'subject_format_subject_id' => $question->subject_format_subject_id,
                'time_to_solve' => $timeToSolve,
            ];

            $examRepo->createQuestions($data);
        });

        $sumTime = $examRepo->getSumTimeForAllExamQuestion();

        $examRepo->update($exam, ['time_to_solve' => $sumTime]);

        return $exam;
    }


    private function checkCompleteAptitudeTest($subjectFormatIds): bool
    {
        return SubjectFormatSubject::whereIn('id', $subjectFormatIds)->pluck('slug')->toArray()
            == array(AptitudeEnums::QUANTITATIVE_SECTION, AptitudeEnums::VERBAL_SECTION);
    }
}
