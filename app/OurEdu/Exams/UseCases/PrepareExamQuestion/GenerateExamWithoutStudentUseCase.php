<?php

namespace App\OurEdu\Exams\UseCases\PrepareExamQuestion;

use App\OurEdu\Exams\Enums\AptitudeEnums;
use App\OurEdu\Exams\Enums\ExamTypes;
use App\Exceptions\ErrorResponseException;
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

class GenerateExamWithoutStudentUseCase extends GenerateExamUseCaseTwo implements GenerateExamUseCaseInterface
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
     * @var Illuminate\Support\Collection
     */
    protected $alreadyTakenQuestsions;

    /**
     * All the prepared Questions
     * @var Illuminate\Support\Collection
     */
    protected $preparedQuestions;

    /**
     * Accepted prepared Questions
     * @var Illuminate\Support\Collection
     */
    protected $acceptedQuestions;

    protected SubjectFormatSubjectRepositoryInterface $subjectFormatSubjectRepository;

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
            $examQuestionRepository,
            $subjectFormatSubjectRepository
        );
        $this->examRepository = $examRepository;
        $this->prepareExamQuestionRepository = $prepareExamQuestionRepository;
        $this->optionRepository = $optionRepository;
        $this->examQuestionRepository = $examQuestionRepository;
        $this->subjectFormatSubjectRepository = $subjectFormatSubjectRepository;
        $this->acceptedQuestions = collect([]);
        $this->subjectFormatQuestions = collect([]);
    }


    public function generateExam(
        $student,
        $subjectId,
        $sectionIds,
        $numberOfQuestions,
        $difficultyLevelId,
        $allSessionID = null
    ) {
        $sections = getSectionsOfSections($sectionIds);

        $difficultyLevel = $this->optionRepository->find($difficultyLevelId)->slug ?? '';
        $sections = $this->subjectFormatSubjectRepository->filterActiveIds($sections);
        $prepareRequiredQuestionsAmount = $this->prepareRequiredQuestionsAmount(
            $student->id,
            $sections,
            $numberOfQuestions,
            $difficultyLevel
        );


        if (isset($prepareRequiredQuestionsAmount['error'])) {
            return $prepareRequiredQuestionsAmount;
        }

        // get subject formats all prepared questsions
        $this->preparedQuestions = $this->prepareExamQuestionRepository->getBySubjectFormats($sections) ?? collect([]);

        // case not enough questions
        if ($numberOfQuestions > $this->preparedQuestions->count()) {
            // throw exception
            throw new ErrorResponseException(
                trans('api.not_enough_questions_change_difficulty_level_or_number_of_questions')
            );
        }

        $countBeforeApplyingDifficultyFilter = $this->preparedQuestions->count();
        $preparedQuestionsBeforeApplyingDifficultyFilter = $this->preparedQuestions;

        // filter prepared questions based on allowed difficulty levels
        $this->difficultyFilter($difficultyLevel);

        // if required number of questions became > prepared question after applying difficulty filter
        // and before applying it was suitable, then rollback that filter
        if ($numberOfQuestions > $this->preparedQuestions->count(
            ) && $countBeforeApplyingDifficultyFilter >= $numberOfQuestions) {
            $this->preparedQuestions = $preparedQuestionsBeforeApplyingDifficultyFilter;
        }


        // take questions based on exam question requiredments
        $this->questionRequirementsFilter($difficultyLevel);

        // after all: if the required $numberOfQuestions is bigger than the acceptedQuestions
        if ($numberOfQuestions > $this->acceptedQuestions->count()) {
            // throw exception
            throw new ErrorResponseException(
                trans('api.not_enough_questions_change_difficulty_level_or_number_of_questions')
            );
        }
        return $this->createExamAndAssignQuestions(
            $student,
            $subjectId,
            $sectionIds,
            $numberOfQuestions,
            $difficultyLevel,
            $allSessionID
        );
    }

    /**
     * Find prepare question count on each subject format
     * and return the counter results to use while choosing questions
     * @param array $subjectFormatSubjectIds
     * @param integer $numberOfQuestions
     * @param string $difficultyLevel
     * @return array|void
     */
    protected function prepareRequiredQuestionsAmount(
        $studentId,
        $subjectFormatSubjectIds,
        $numberOfQuestions,
        $difficultyLevel
    ) {
        if ($subjectFormatSubjectIds) {
            if (count($subjectFormatSubjectIds) > $numberOfQuestions) {
                $oldSubjectFormatSubjectIds = $subjectFormatSubjectIds;
                $subjectFormatSubjectIdsRandomKeys = array_rand($oldSubjectFormatSubjectIds, $numberOfQuestions);
                $subjectFormatSubjectIds = [];
                foreach ($subjectFormatSubjectIdsRandomKeys as $subjectFormatSubjectIdsRandomKey) {
                    $subjectFormatSubjectIds[] = $oldSubjectFormatSubjectIds[$subjectFormatSubjectIdsRandomKey];
                }
            }
            $result = $this->prepareExamQuestionRepository->getSubjectFormatQuestionsCount(
                $subjectFormatSubjectIds,
                $numberOfQuestions,
                [$difficultyLevel],
            );
        }

        if (!count($result)) {
            return [
                'error' => true,
                'status' => 422,
                'title' => trans('exam.Exam not created, use different criteria'),
                'detail' => trans('exam.Exam not created, use different criteria'),
            ];
        }
        $resultCollection = collect($result);
        $resultCollection = $resultCollection->sortBY('return_question_count');

        $returnCountSum = $resultCollection->sum('return_question_count');

        // case each question total is not the same as the required questions count
        if ($returnCountSum > $numberOfQuestions) {
            $resultCollection->last(function ($item) use ($numberOfQuestions, $returnCountSum) {
                return $item->return_question_count =
                    $item->return_question_count - ($returnCountSum - $numberOfQuestions);
            });
        }

        $this->questionRequirements = $resultCollection;
    }


    /**
     * Filter prepared questions and return allowed levels
     * @param string $difficultyLevel
     * @return void
     */
    protected function difficultyFilter(string $difficultyLevel)
    {
        if (!in_array($difficultyLevel, DifficultlyLevelEnums::availableDifficultlyLevel())) {
            throw new ErrorResponseException(trans('app.Unknown difficulty level'));
        }

        // allowed difficulty levels
        $allowedLevels = [];

        if ($difficultyLevel == DifficultlyLevelEnums::DIFFICULT) {
            $allowedLevels = [
                DifficultlyLevelEnums::DIFFICULT,
                DifficultlyLevelEnums::MEDIUM,
                DifficultlyLevelEnums::EASY,
            ];
        }

        if ($difficultyLevel == DifficultlyLevelEnums::MEDIUM) {
            $allowedLevels = [
                DifficultlyLevelEnums::MEDIUM,
                DifficultlyLevelEnums::EASY,
            ];
        }

        if ($difficultyLevel == DifficultlyLevelEnums::EASY) {
            $allowedLevels = [
                DifficultlyLevelEnums::EASY,
            ];
        }

        $this->preparedQuestions = $this->preparedQuestions->whereIn('difficulty_level', $allowedLevels);
    }


    /**
     * Filter prepared questions based on question requirements
     * @param integer $numberOfQuestions
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

    protected function createExamAndAssignQuestions(
        $student,
        $subjectId,
        $sectionIds,
        $numberOfQuestions,
        $difficultyLevel,
        $allSessionID = null
    ) {
        $title = $this->getExamTitleFromSubjectFormats($sectionIds);
        $exam = $this->examRepository->create([
            'student_id' => null,
            'type' => ExamTypes::EXAM,
            'title' => $title,
            'questions_number' => $numberOfQuestions,
            'difficulty_level' => $difficultyLevel,
            'subject_id' => $subjectId,
            'vcr_session_id' => $allSessionID,
            'subject_format_subject_id' => json_encode($sectionIds)

        ]);

        $examRepo = new ExamRepository($exam);

        if ($this->acceptedQuestions->count() > $numberOfQuestions) {
            $this->acceptedQuestions = $this->acceptedQuestions->slice(0, $numberOfQuestions);
        }
        $this->acceptedQuestions->each(function ($question) use ($examRepo, $exam) {
            $timeToSolve = !is_null($question->time_to_solve)
                ? $question->time_to_solve :
                env('exam_default_question_time', 30);

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

}
