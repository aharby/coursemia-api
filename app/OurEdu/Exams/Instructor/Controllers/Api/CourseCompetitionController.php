<?php

namespace App\OurEdu\Exams\Instructor\Controllers\Api;

use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Exams\Instructor\Middleware\IsCourseInstructorMiddleware;
use App\OurEdu\Exams\Instructor\Requests\GenerateCourseCompetitionRequest;
use App\OurEdu\Exams\Instructor\Transformers\CourseCompetitionStudentsTransformer;
use App\OurEdu\Exams\Instructor\Transformers\CourseCompetitionTransformer;
use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Exams\Repository\Exam\ExamRepository;
use App\OurEdu\Exams\Repository\ExamQuestion\ExamQuestionRepositoryInterface;
use App\OurEdu\Exams\UseCases\PrepareExamQuestion\GenerateExamUseCase\GenerateExamUseCaseInterface;
use App\OurEdu\Exams\UseCases\StartExamUseCase\StartExamUseCaseInterface;
use Illuminate\Support\Facades\DB;
use Swis\JsonApi\Client\Interfaces\ParserInterface;

class CourseCompetitionController extends BaseApiController
{
    public function __construct(
        private GenerateExamUseCaseInterface $generateExamUseCaseInterface,
        private ParserInterface $parserInterface,
        private ExamRepository $examRepository
    ) {
        $this->middleware(IsCourseInstructorMiddleware::class)->only('generateCourseCompetition');
    }

    public function generateCourseCompetition(GenerateCourseCompetitionRequest $request, Course $course)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();
        DB::beginTransaction();

        $exam = $this->generateExamUseCaseInterface->generateCourseCompetition($course, $data);

        if (isset($exam['error'])) {
            return formatErrorValidation($exam['error']);
        }

        DB::commit();

        $meta = ['message' => trans('api.Competition generated')];

        return $this->transformDataModInclude(
            $exam,
            '',
            new CourseCompetitionTransformer(),
            ResourceTypesEnums::COURSE_COMPETITION,
            $meta
        );
    }

    public function all()
    {
        $page = request()->input('page', 1);
        $perPage = request()->input('per_page', env('PAGE_LIMIT', 20));

        $exams = $this->examRepository->getInstructorCourseCompetitions(auth()->user(), $perPage, $page);

        return $this->transformDataModInclude(
            $exams,
            'actions',
            new CourseCompetitionTransformer(),
            ResourceTypesEnums::COURSE_COMPETITION,
        );
    }
    public function index()
    {
        $page = request()->input('page', 1);
        $perPage = request()->input('per_page', env('PAGE_LIMIT', 20));

        $exams = $this->examRepository->getFinishedInstructorCourseCompetitions(auth()->user(), $perPage, $page);

        return $this->transformDataModInclude(
            $exams,
            'actions',
            new CourseCompetitionTransformer(),
            ResourceTypesEnums::COURSE_COMPETITION,
        );
    }

    public function feedBack(Exam $exam)
    {
        if (!$exam->is_finished) {
            $return = [];
            $return['status'] = 422;
            $return['detail'] = trans('api.The exam is not finished');
            $return['title'] = 'The exam is not finished';

            return $return;
        }
        $rank = 1;

        $previous = null;

        $students = $exam->competitionStudents()->with('user')
            ->orderByPivot('result', 'DESC')->get();
        foreach ($students as $key => $student) {
            if ($previous && $previous->pivot->result != $student->pivot->result) {
                $rank++;
            }

            $student->order = $rank;
            $previous = $student;
        }

        return $this->transformDataModInclude(
            $students,
            '',
            new CourseCompetitionStudentsTransformer($exam),
            ResourceTypesEnums::COURSE_COMPETITION_STUDENTS
        );
    }
}
