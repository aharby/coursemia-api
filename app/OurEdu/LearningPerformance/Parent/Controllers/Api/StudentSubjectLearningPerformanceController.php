<?php

namespace App\OurEdu\LearningPerformance\Parent\Controllers\Api;

use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Exams\Enums\ExamTypes;
use App\OurEdu\Exams\UseCases\RequestLiveSessionUseCase\RequestLiveSessionUseCaseInterface;
use App\OurEdu\LearningPerformance\LearningPerformance;
use App\OurEdu\LearningPerformance\Parent\Middlewares\Api\ParentInRelationMiddleware;
use App\OurEdu\LearningPerformance\Parent\Transformers\ActivitiesLogTransformer;
use App\OurEdu\LearningPerformance\Parent\Transformers\ExamPerformanceTransformer;
use App\OurEdu\LearningPerformance\Parent\Transformers\StudentFeedbackTransformer;
use App\OurEdu\LearningPerformance\Parent\Transformers\TimeTransformer;
use App\OurEdu\LearningPerformance\UseCases\StudentOrderUseCase\StudentOrderUseCaseInterface;
use App\OurEdu\LearningPerformance\UseCases\StudentSuccessRateUseCase\StudentSuccessRateUseCaseInterface;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Subjects\Models\SubModels\SubjectTime;
use App\OurEdu\Subjects\Repository\SubjectRepositoryInterface;
use App\OurEdu\Users\Repository\StudentRepositoryInterface;

class StudentSubjectLearningPerformanceController extends BaseApiController
{

    private $studentSuccessRateUseCase;
    private $learningPerformance;
    private $studentRepository;
    private $subjectRepository;
    private $StudentOrderUseCase;
    /**
     * @var RequestLiveSessionUseCaseInterface
     */
    private $requestLiveSessionUseCase;

    public function __construct(
        StudentOrderUseCaseInterface $StudentOrderUseCase,
        StudentRepositoryInterface $studentRepository,
        SubjectRepositoryInterface $subjectRepository,
        LearningPerformance $learningPerformance,
        StudentSuccessRateUseCaseInterface $studentSuccessRateUseCase,
        RequestLiveSessionUseCaseInterface $requestLiveSessionUseCase
    )
    {
        $this->studentRepository = $studentRepository;
        $this->subjectRepository = $subjectRepository;
        $this->studentSuccessRateUseCase = $studentSuccessRateUseCase;
        $this->learningPerformance = $learningPerformance;
        $this->middleware(ParentInRelationMiddleware::class)
            ->only(['studentFeedback']);
        $this->requestLiveSessionUseCase = $requestLiveSessionUseCase;
        $this->StudentOrderUseCase = $StudentOrderUseCase;
    }

    public function activityLogPerformance($studentId, $subjectId)
    {
        $studentObj = $this->studentRepository->findOrFail($studentId);
        $this->learningPerformance->student = $studentObj->load('user');
        $this->learningPerformance->subject = $this->subjectRepository->findOrFail($subjectId);
        $activities = $this->learningPerformance->student->events()
        ->when(request('date_from'), function ($q) {
            $q->whereDate('created_at', '>=', startOfDay(request('date_from')));
        })
        ->when(request('date_to'), function ($q) {
            $q->whereDate('created_at', '<=', endOfDay(request('date_to')));
        })
        ->where('event_properties->subject_attributes->subject_id', $this->learningPerformance->subject->id)
        ->latest()->paginate(10, ['*'], 'activity-page');

        $meta = ['pagination' => [
            'per_page' => $activities->perPage(),
            'total' => $activities->total(),
            'current_page' => $activities->currentPage(),
            'count' => $activities->count(),
            'total_pages' => $activities->lastPage(),
            'next_page' => $activities->nextPageUrl(),
            'previous_page' => $activities->previousPageUrl(),
        ]];
        return $this->transformDataModInclude($activities, 'activityLog',
            new ActivitiesLogTransformer(),
            ResourceTypesEnums::ACTIVITY_LOG,$meta);
    }

    public function examPerformance($studentId, $subjectId)
    {
        $studentObj = $this->studentRepository->findOrFail($studentId);
        $this->learningPerformance->student = $studentObj->load('user');
        $this->learningPerformance->subject = $this->subjectRepository->findOrFail($subjectId);
        $exams = $this->learningPerformance->subject->exams()
                ->where('type', ExamTypes::EXAM)
                ->where('student_id', $this->learningPerformance->student->id)
                ->when(request('date_from'), function ($q)  {
                    $q ->whereDate('created_at' , '>=' , startOfDay(request('date_from')));
                })
                ->when(request('date_to'), function ($q)  {
                    $q ->whereDate('created_at' , '<=' , endOfDay(request('date_to')));
                })
                ->jsonPaginate(request("per_page") ?? 10, ['*'], 'exams-page');

        $meta = ['pagination' => [
            'per_page' => $exams->perPage(),
            'total' => $exams->total(),
            'current_page' => $exams->currentPage(),
            'count' => $exams->count(),
            'total_pages' => $exams->lastPage(),
            'next_page' => $exams->nextPageUrl(),
            'previous_page' => $exams->previousPageUrl(),
        ]];
        return $this->transformDataModInclude($exams, '',  new ExamPerformanceTransformer(),
            ResourceTypesEnums::EXAM_PERFORMANCE,$meta);
    }


    public function timesPerformance($studentId, $subjectId)
    {
        $studentObj = $this->studentRepository->findOrFail($studentId);
        $this->learningPerformance->student = $studentObj->load('user');
        $this->learningPerformance->subject = $this->subjectRepository->findOrFail($subjectId);
        $times = SubjectTime::where('subject_id' , $this->learningPerformance->subject->id)
            ->where('student_id' , $this->learningPerformance->student->id)
            ->when(request('date_from'), function ($q)  {
                $q ->whereDate('created_at' , '>=' , startOfDay(request('date_from')));
            })
            ->when(request('date_to'), function ($q)  {
                $q ->whereDate('created_at' , '<=' , endOfDay(request('date_to')));
            })
            ->latest()->paginate(10, ['*'], 'time-page');

        $meta = ['pagination' => [
            'per_page' => $times->perPage(),
            'total' => $times->total(),
            'current_page' => $times->currentPage(),
            'count' => $times->count(),
            'total_pages' => $times->lastPage(),
            'next_page' => $times->nextPageUrl(),
            'previous_page' => $times->previousPageUrl(),
        ]];
        return $this->transformDataModInclude($times, '',  new TimeTransformer(),
            ResourceTypesEnums::SUBJECT_TIME,$meta);
    }

    private function getStudentOrderInSubject($studentId, $subjectId) {
        if(isset($studentId)){
            $res = $this->StudentOrderUseCase->getStudentOrderInSubject($subjectId);
            $order = array_search($studentId, array_keys($res));
        }
        return (is_bool($order) ? 0 : $order + 1);
    }


    public function studentFeedback($studentId, $subjectId)
    {
        $studentObj = $this->studentRepository->findOrFail($studentId);
        $this->learningPerformance->student = $studentObj->load('user');
        $this->learningPerformance->subject = $this->subjectRepository->findOrFail($subjectId);

        $this->learningPerformance->success_rate =
            $this->studentSuccessRateUseCase
                ->getSuccessRateOnSubject($studentId, $subjectId);

        // speed_percentage order according to all students
        $this->learningPerformance->solving_speed_percentage_order =
            $this->studentSuccessRateUseCase
                ->getStudentSpeedOrderOfSolvingExams($studentId, $subjectId);
        
        $this->learningPerformance->countStudentsBySolvingSpeed = 
            $this->studentSuccessRateUseCase-> getStudentCountSpeedSolvingExamsInSubject($subjectId);       

        // subject progress percentage order according to all students
        $this->learningPerformance->subject_progress_percentage_order =
            $this->studentSuccessRateUseCase
                ->getSubjectProgressPercentage($studentId, $subjectId);
        
        $this->learningPerformance->studentsProgressCount =
            $this->studentSuccessRateUseCase->getStudentsCountProgressInSubject($subjectId);        
        // exams count order according to all students
        $this->learningPerformance->exams_count_order =
            $this->studentSuccessRateUseCase
                ->getExamsCountsOrder($studentId, $subjectId);
               
        $this->learningPerformance->countExamStudents =  
            $this->studentSuccessRateUseCase->getExamCountStudent($subjectId);
        
        $this->learningPerformance->studentOrderInGeneralExams = 
            $this->getStudentOrderInSubject($studentId, $subjectId);

        $this->learningPerformance->studentInGeneralExamsCount =
            $this->StudentOrderUseCase->getStudentGeneralExamsCount($subjectId);

        $subject = Subject::find($subjectId);

        if($studentObj->user) {
            $this->learningPerformance->course_sessions_count = $this->studentRepository->getStudentCourseSessions($studentObj->user,$subject);
            $this->learningPerformance->live_sessions_count = $this->studentRepository->getStudentLiveSessions($studentObj->user,$subject);
            $this->learningPerformance->requested_sessions_count = $this->studentRepository->getStudentRequestedSessions($studentObj->user,$subject);
            $this->learningPerformance->courses = $this->studentRepository->getStudentCourseAttendance($studentObj,$subject);
        }

        return $this->transformDataModInclude($this->learningPerformance, 'course_sessions.course',
            new StudentFeedbackTransformer(),
            ResourceTypesEnums::STUDENT_FEEDBACK);
    }
}
