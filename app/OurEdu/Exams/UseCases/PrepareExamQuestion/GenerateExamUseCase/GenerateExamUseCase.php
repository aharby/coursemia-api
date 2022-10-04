<?php

namespace App\OurEdu\Exams\UseCases\PrepareExamQuestion\GenerateExamUseCase;

use App\Exceptions\ErrorResponseException;
use App\OurEdu\Courses\Enums\CourseEnums;
use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Exams\Enums\ExamTypes;
use App\OurEdu\Exams\Events\CourseCompetitionEvents\StartCourseCompetition;
use App\OurEdu\Exams\Instructor\Jobs\FinishCourseCompetitionJob;
use App\OurEdu\Exams\Instructor\Jobs\SetCompetitionAsStartedJob;
use App\OurEdu\Exams\Instructor\Jobs\StartCourseCompetitionJob;
use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Exams\Models\PrepareExamQuestion;
use App\OurEdu\Exams\Repository\Exam\ExamRepository;
use App\OurEdu\Exams\Repository\Exam\ExamRepositoryInterface;
use App\OurEdu\Exams\Repository\PrepareExamQuestion\PrepareExamQuestionRepositoryInterface;
use App\OurEdu\Exams\Student\Jobs\StudentFinishedCompetitionJob;
use App\OurEdu\LearningResources\Enums\DifficultlyLevelEnums;
use App\OurEdu\Options\Repository\OptionRepositoryInterface;
use App\OurEdu\Subjects\Repository\SubjectFormatSubjectRepositoryInterface;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

abstract class GenerateExamUseCase implements GenerateExamUseCaseInterface
{
    protected ExamRepositoryInterface $examRepository;
    protected PrepareExamQuestionRepositoryInterface $prepareExamQuestionRepository;
    protected OptionRepositoryInterface $optionRepository;
    protected SubjectFormatSubjectRepositoryInterface $subjectFormatSubjectRepository;

    /**
     * Accepted prepared Questions
     * @var collection
     */
    protected $acceptedQuestions;

    /**
     * @var string
     */
    protected $generationType;
    protected $pivotName;

    public function __construct(
        ExamRepositoryInterface $examRepository,
        PrepareExamQuestionRepositoryInterface $prepareExamQuestionRepository,
        OptionRepositoryInterface $optionRepository,
        SubjectFormatSubjectRepositoryInterface $subjectFormatSubjectRepository
    ) {
        $this->examRepository = $examRepository;
        $this->prepareExamQuestionRepository = $prepareExamQuestionRepository;
        $this->optionRepository = $optionRepository;
        $this->subjectFormatSubjectRepository = $subjectFormatSubjectRepository;
        $this->acceptedQuestions = collect();
    }

    abstract public function generateExam(
        $student,
        $subjectId,
        $sectionIds,
        $numberOfQuestions,
        $difficultyLevelId,
    );

    public function generateCompetition(
        $student,
        $subjectId,
        $sectionIds,
        $numberOfQuestions,
        $difficultyLevelId
    ) {
    }


    /**
     * @param $student
     * @param $subjectId
     * @param $sectionIds
     * @return Exam|array
     */
    public function generatePractice($student, $subjectId, $sectionIds)
    {
        $sections = getSectionsOfSections($sectionIds);
        $validate = $this->checkPracticeQuestionsAmount($sections, $student->id);
        if (isset($validate['error'])) {
            return $validate;
        }
        
        $title = $this->getExamTitleFromSubjectFormats($sectionIds);
        $exam = $this->examRepository->create([
            'student_id' => $student->id,
            'title' => $title,
            'type' => ExamTypes::PRACTICE,
            'subject_id' => $subjectId,
            'subject_format_subject_id' => json_encode($sectionIds)
        ]);

        $examRepo = new ExamRepository($exam);

        //use model direct for using chunk function to avoid fail if number of question is big
        PrepareExamQuestion::query()
            ->whereIn('subject_format_subject_id', $sections)
            ->where('subject_id', $subjectId)
            ->where('is_done', 1)
            ->inRandomOrder()
            ->whereDoesntHave("practiceStudents", fn($q) => $q->where('id', $student->id))
            ->chunk(50, function ($questions) use ($examRepo, $exam) {
                $questions->each(function ($question) use ($examRepo, $exam) {
                    $data = [
                        'slug' => $question->question_type,
                        'exam_id' => $exam->id,
                        'question_type' => $question->question_type,
                        'question_table_type' => $question->question_table_type,
                        'question_table_id' => $question->question_table_id,
                        'subject_id' => $question->subject_id,
                        'subject_format_subject_id' => $question->subject_format_subject_id,
                    ];
                    $examRepo->createQuestions($data);
                });
            });

        $examRepo->update($exam, [
            'questions_number' => $examRepo->getQuestionCount()
        ]);

        return $exam;
    }

    private function checkPracticeQuestionsAmount($sectionIds, $studentId)
    {
        $levels = $this->difficultyFilter(DifficultlyLevelEnums::DIFFICULT);
        $questionsCount = PrepareExamQuestion::whereIn('subject_format_subject_id', $sectionIds)
            ->whereDoesntHave("practiceStudents", fn($q) => $q->where('id', $studentId))
            ->where('is_done', 1)->count();

        if ($questionsCount <= 0) {
            $detachQuestionIds = $this->prepareExamQuestionRepository->detachQuestions(
                $studentId,
                $sectionIds,
                $levels,
                'practice'
            );
            if (count($detachQuestionIds)) {
                return $this->checkPracticeQuestionsAmount($sectionIds, $studentId);
            }

            return [
                'error' => true,
                'status' => 422,
                'message' => trans('exam.Practice not created, use different criteria'),
                'detail' => trans('exam.Practice not created, use different criteria'),
                'title' => trans('exam.Practice not created, use different criteria'),
            ];
        }

        return $questionsCount;
    }


    /**
     * @param $instructorId
     * @param $subjectId
     * @param $sectionIds
     * @param $numberOfQuestions
     * @param $difficultyLevelId
     * @param VCRSession $vcrSession
     * @return Exam|array
     */
    public function generateInstructorCompetition(
        $instructorId,
        $subjectId,
        $sectionIds,
        $numberOfQuestions,
        $difficultyLevelId,
        VCRSession $vcrSession
    ) {
        if (!empty($this->validateCompetitionSession($vcrSession))) {
            return $this->validateCompetitionSession($vcrSession);
        }
        $sections = getSectionsOfSections($sectionIds);

        $difficultyLevel = $this->optionRepository->find($difficultyLevelId)->slug ?? '';

        $resultCollection = $this->prepareExamQuestionRepository->getSubjectFormatQuestionsCount(
            $sections,
            $numberOfQuestions,
            [$difficultyLevel],
            'prepare_competition_question_student'
        );

        if (!count($resultCollection)) {
            return [
                'error' => true,
                'status' => 422,
                'message' => trans('exam.Competition not created, use different criteria'),
                'detail' => trans('exam.Competition not created, use different criteria'),
                'title' => trans('exam.Competition not created, use different criteria'),
            ];
        }

        $resultCollection = $resultCollection->sortBY('return_question_count');

        $questions = new Collection();
        $resultCollection->each(function ($item) use ($difficultyLevel, &$questions) {
            $questions = $questions->merge(
                $this->prepareExamQuestionRepository->getBySubjectFormatAndDifficultyLevel(
                    $item->subject_format_subject_id,
                    $difficultyLevel,
                    $item->return_question_count
                )
            );
        });

        $title = $this->getExamTitleFromSubjectFormats($sectionIds);

        $exam = $this->examRepository->create([
            'creator_id' => $instructorId,
            'type' => ExamTypes::INSTRUCTOR_COMPETITION,
            'title' => $title,
            'questions_number' => $numberOfQuestions,
            'difficulty_level' => $difficultyLevel,
            'subject_id' => $subjectId,
            'subject_format_subject_id' => json_encode($sectionIds),
            'vcr_session_id' => $vcrSession->id ?? null,
        ]);
        $examRepo = new ExamRepository($exam);

        $questions->each(function ($question) use ($examRepo, $exam) {
            $timeToSolve = !is_null($question->time_to_solve) ? $question->time_to_solve
                : env('TIME_TO_SOLVE_QUESTION', 30);
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
     * @param VCRSession $session
     * @return array
     */
    public function validateCompetitionSession(VCRSession $session)
    {
        $error = [];
        if ($session->exam) {
            $error['message'] = trans('exam.exam exists');
            $error['error'] = true;
            return $error;
        }
        $from = Carbon::parse($session->time_to_start);
        $to = Carbon::parse($session->time_to_end);
        if (!Carbon::now()->between($from, $to)) {
            $error['message'] = trans('exam.cannot generate time outside the session time');
            $error['error'] = true;
            return $error;
        }
        if (!$session->course) {
            $error['message'] = trans('exam.session is not valid');
            $error['error'] = true;
            return $error;
        }
        if ($session->course->type == CourseEnums::PUBLIC_COURSE) {
            $error['message'] = trans('exam.session is not valid');
            $error['error'] = true;
            return $error;
        }

        return $error;
    }

    /**
     * Find prepare question count on each subject format
     * and return the counter results to use while choosing questions
     * @param $studentId
     * @param array $sectionIds
     * @param int $numberOfQuestions
     * @param $levels
     * @return array
     */
    protected function prepareRequiredQuestionsAmount($studentId, $sectionIds, $numberOfQuestions, $levels)
    {
        if (count($sectionIds) > $numberOfQuestions) {
            $sectionIds = Arr::random($sectionIds, $numberOfQuestions);
        }

        $resultCollection = $this->prepareExamQuestionRepository->getSubjectFormatQuestionsCount(
            $sectionIds,
            $numberOfQuestions,
            $levels,
            $this->pivotName,
            $studentId,
        );

        if (
            !count($resultCollection) ||
            $resultCollection->sum('return_question_count') < $numberOfQuestions ||
            $resultCollection->sum('question_count') < $numberOfQuestions
        ) {
            $repeatQuestionIds = $this->repeatQuestions($resultCollection, $studentId, $levels, $sectionIds);
            if (count($repeatQuestionIds)) {
                return $this->prepareRequiredQuestionsAmount($studentId, $sectionIds, $numberOfQuestions, $levels);
            }
            return [
                'error' => true,
                'status' => 422,
                'message' => trans('exam.Exam not created, use different criteria'),
                'detail' => trans('exam.Exam not created, use different criteria'),
                'title' => trans('exam.Exam not created, use different criteria'),
            ];
        }

        return $resultCollection
            ->filter(fn($value) => $value->return_question_count > 0)
            ->sortBY('return_question_count')->toArray();
    }


    /**
     * Filter prepared questions and return allowed levels
     * @param string $difficultyLevel
     * @return array
     * @throws ErrorResponseException
     */
    protected function difficultyFilter(string $difficultyLevel)
    {
        if (!in_array($difficultyLevel, DifficultlyLevelEnums::availableDifficultlyLevel())) {
            throw new ErrorResponseException(trans('app.Unknown difficulty level'));
        }

        // allowed difficulty levels
        return match ($difficultyLevel) {
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
            'type' => $this->generationType,
            'title' => $title,
            'difficulty_level' => $difficultyLevel,
            'subject_id' => $subjectId,
            'subject_format_subject_id' => json_encode($sectionIds)
        ]);

        $examRepo = new ExamRepository($exam);

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

        $examRepo->update($exam, [
            'time_to_solve' => $sumTime,
            'questions_number' => $numberOfQuestions ?? $examRepo->getQuestionCount()
        ]);

        return $exam;
    }

    /**
     * @param $resultCollection
     * @param $studentId
     * @param $levels
     * @param $sectionIds
     * @return array
     */
    protected function repeatQuestions(
        $resultCollection,
        $studentId,
        $levels,
        $sectionIds
    ) {
        $sortedSectionIds = $resultCollection->sortBy('return_question_count')
            ->pluck('subject_format_subject_id')->all();

        foreach ($sortedSectionIds as $sectionId) {
            $detachedQuestionIds = $this->prepareExamQuestionRepository->detachQuestions(
                $studentId,
                [$sectionId],
                $levels,
                $this->generationType
            );

            if ($detachedQuestionIds) {
                return $detachedQuestionIds;
            }
        }

        return $this->prepareExamQuestionRepository->detachQuestions(
            $studentId,
            $sectionIds,
            $levels,
            $this->generationType
        );
    }

    /**
     * @param $sectionIds
     * @return string
     */
    protected function getExamTitleFromSubjectFormats($sectionIds)
    {
        $sections = $this->subjectFormatSubjectRepository
            ->getSectionsByIds($sectionIds);
        return getTitleFromSections($sections);
    }

    /**
     * @param $arr
     * @return float
     */
    public function standDeviation($arr)
    {
        $numOfElements = count($arr);

        $variance = 0.0;

        // calculating mean using array_sum() method
        $average = array_sum($arr) / $numOfElements;

        foreach ($arr as $i) {
            // sum of squares of differences between
            // all numbers and means.
            $variance += pow(($i - $average), 2);
        }

        return (float)sqrt($variance / $numOfElements);
    }

    public function getPercentOfNumber($number, $percent)
    {
        return (($percent / 100) * $number);
    }

    public function getNumberOfPercent($numberFour, $total)
    {
        return ($numberFour / $total) * 100;
    }

    public function generateCourseCompetition(Course $course, $data)
    {
        $numberOfQuestions = $data->number_of_questions;
        $difficultyLevelId = $data->difficulty_level;
        $sectionIds = $data->subject_format_subject_ids;
        $subjectId = $course->subject_id;

        $validations  = $this->validateCourseCompetition($course);
        if (count($validations)) {
            return $validations;
        }
        $sections = getSectionsOfSections($sectionIds);

        $difficultyLevel = $this->optionRepository->find($difficultyLevelId)->slug ?? '';

        $questions = $this->prepareExamQuestionRepository->getBySubjectSectionsAndDifficultyLevel(
            $sections,
            $difficultyLevel,
            $numberOfQuestions
        );

        if (!count($questions)) {
            return [
                'error' => [
                    'status' => 422,
                    'detail' => trans('exam.Competition not created, use different criteria'),
                    'title' => 'Competition not created, use different criteria',
                ]
            ];
        }

        $title = $this->getExamTitleFromSubjectFormats($sectionIds);

        $exam = $this->examRepository->create([
            'creator_id' => $course->instructor_id,
            'type' => ExamTypes::COURSE_COMPETITION,
            'title' => $title,
            'questions_number' => $numberOfQuestions,
            'difficulty_level' => $difficultyLevel,
            'subject_id' => $subjectId,
            'subject_format_subject_id' => json_encode($sectionIds),
            'course_id' => $course->id,
            'start_time' => $data->start_time,
            'finished_time' => $data->end_time
        ]);
        $examRepo = new ExamRepository($exam);

        $questions->each(function ($question) use ($examRepo, $exam) {
            $timeToSolve = !is_null($question->time_to_solve) ? $question->time_to_solve
                : env('TIME_TO_SOLVE_QUESTION', 30);
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

        $students = Student::query()->with('user')
            ->whereIn('id', $data->students)
            ->get()
            ->pluck('user');

        $exam->courseCompetitionStudents()->sync($data->students);

        StartCourseCompetitionJob::dispatch($exam, $students)
            ->delay(
                Carbon::parse($exam->start_time)
                    ->subMinutes(5)
            );
        SetCompetitionAsStartedJob::dispatch($exam)->delay(Carbon::parse($exam->start_time));
        FinishCourseCompetitionJob::dispatch($exam)->delay(Carbon::parse($exam->finished_time));

        return $exam;
    }

    private function validateCourseCompetition(Course $course)
    {
        $error = [];

        if ($course->type !== CourseEnums::SUBJECT_COURSE) {
            $error['error'] = [
                'status' => 422,
                'detail' => trans('exam.this course not subject course can not create competition on it'),
                'title'=> 'this course not subject course can not create competition on it',
            ];

            return $error;
        }

        $from = Carbon::parse($course->start_date);
        $to = Carbon::parse($course->end_date);
        if (!Carbon::now()->between($from, $to)) {

            $error['error'] = [
                'status' => 422,
                'detail' => trans('exam.Can not generate competition outside the course time'),
                'title'=> 'Can not generate competition outside the course time',
            ];

            return $error;
        }

        return $error;
    }

}
