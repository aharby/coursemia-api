<?php

namespace App\OurEdu\GeneralQuizzes\CourseHomework\Student\Controllers\Api;

use App\OurEdu\Users\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum;
use App\OurEdu\GeneralQuizzes\CourseHomework\Student\Middleware\IsSubscripedCourse;
use App\OurEdu\GeneralQuizzes\CourseHomework\Student\Transformers\StudentListTransformer;
use App\OurEdu\GeneralQuizzes\CourseHomework\Student\Transformers\CourseHomeWorkTransformer;
use App\OurEdu\GeneralQuizzes\CourseHomework\Student\Transformers\StudentAnswersTransformer;
use App\OurEdu\GeneralQuizzes\CourseHomework\Student\Transformers\StudentAnswerListTransformer;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizStudentRepositoryInterface;

class CourseHomeworkController extends BaseApiController
{
    private array $filters;

    public function __construct(
        private GeneralQuizRepositoryInterface $generalQuizRepository,
        private GeneralQuizStudentRepositoryInterface $generalQuizStudentRepository
    ) {
        $this->middleware('auth:api');
        $this->middleware('type:student');
        $this->middleware(IsSubscripedCourse::class);
    }

    public function listHomeworks()
    {
        $this->setFilters();
        $homeworks = $this->generalQuizRepository
            ->listStudentAvailableGeneralQuizzes(GeneralQuizTypeEnum::COURSE_HOMEWORK, $this->filters);
        return $this->transformDataModInclude(
            $homeworks,
            'course',
            new CourseHomeWorkTransformer(),
            ResourceTypesEnums::HOMEWORK,
            $this->filters
        );
    }

    public function listHomeworksReport(Request $request)
    {
        $this->setFilters();
        $courseId = $request->input('course_id');
       
        $courseHomework = $this->generalQuizRepository
            ->getGeneralQuizzesStudents(Auth::user(), GeneralQuizTypeEnum::COURSE_HOMEWORK, $courseId);
            return $this->transformDataModInclude(
                $courseHomework,
                '',
                new StudentListTransformer(),
                ResourceTypesEnums::COURSE_HOMEWORK,
                $this->filters
            );
    }

    public function getAnswersStudent(GeneralQuiz $courseHomework)
    {
        $studentGeneralQuiz = $this->generalQuizStudentRepository->findStudentGeneralQuiz($courseHomework->id, auth()->id());
        return $this->transformDataModInclude(
            $studentGeneralQuiz,
            'questions',
            new StudentAnswersTransformer($courseHomework, auth()->user()),
            ResourceTypesEnums::HOMEWORK_Student
        );
    }

    public function getStudentAnswersSolved(GeneralQuiz $courseHomework)
    {
        return $this->transformDataModInclude(
            ['data' => 'fale'],
            '',
            new StudentAnswerListTransformer($courseHomework, auth()->user()),
            ResourceTypesEnums::HOMEWORK
        );
    }

    public function setFilters()
    {
        $this->filters[] = [
            'name' => 'course_id',
            'type' => 'select',
            'data' => auth()->user()->student->courses->pluck('name', 'id')->toArray(),
            'trans' => false,
            'value' => request()->get('course_id'),
        ];
    }
}
