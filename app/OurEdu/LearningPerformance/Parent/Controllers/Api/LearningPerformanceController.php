<?php

namespace App\OurEdu\LearningPerformance\Parent\Controllers\Api;

use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Courses\Enums\CourseSessionEnums;
use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Exams\Repository\Exam\ExamRepositoryInterface;
use App\OurEdu\Exams\UseCases\RequestLiveSessionUseCase\RequestLiveSessionUseCaseInterface;
use App\OurEdu\LearningPerformance\LearningPerformance;
use App\OurEdu\LearningPerformance\Parent\Middlewares\Api\ParentInRelationMiddleware;
use App\OurEdu\LearningPerformance\Parent\Transformers\ActivitiesLogTransformer;
use App\OurEdu\LearningPerformance\Parent\Transformers\ExamPerformanceTransformer;
use App\OurEdu\LearningPerformance\Parent\Transformers\ListsTransformers\CoursesListTransformer;
use App\OurEdu\LearningPerformance\Parent\Transformers\ListsTransformers\PackagesListTransformer;
use App\OurEdu\LearningPerformance\Parent\Transformers\ListsTransformers\SubjectsListTransformer;
use App\OurEdu\LearningPerformance\Parent\Transformers\StudentAllSubjectsPerformanceTransformer;
use App\OurEdu\LearningPerformance\Parent\Transformers\StudentPerformanceTransformer;
use App\OurEdu\LearningPerformance\Parent\Transformers\StudentSubjectPerformanceTransformer;
use App\OurEdu\LearningPerformance\UseCases\StudentOrderUseCase\StudentOrderUseCaseInterface;
use App\OurEdu\LearningPerformance\UseCases\StudentSuccessRateUseCase\StudentSuccessRateUseCaseInterface;
use App\OurEdu\SubjectPackages\Package;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Subjects\Repository\SubjectRepositoryInterface;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Users\Repository\StudentRepositoryInterface;
use Illuminate\Support\Facades\Auth;
Use Illuminate\Support\Facades\DB;
use App\OurEdu\Users\User;

class LearningPerformanceController extends BaseApiController
{

    private $StudentOrderUseCase;
    private $studentSuccessRateUseCase;
    private $learningPerformance;
    private $studentRepository;
    private $subjectRepository;
    private $examRepository;
    /**
     * @var RequestLiveSessionUseCaseInterface
     */
    private $requestLiveSessionUseCase;

    public function __construct(
        StudentOrderUseCaseInterface $StudentOrderUseCase,
        StudentRepositoryInterface $studentRepository,
        SubjectRepositoryInterface $subjectRepository,
        ExamRepositoryInterface $examRepository,
        LearningPerformance $learningPerformance,
        StudentSuccessRateUseCaseInterface $studentSuccessRateUseCase,
        RequestLiveSessionUseCaseInterface $requestLiveSessionUseCase
    )
    {
        $this->studentRepository = $studentRepository;
        $this->subjectRepository = $subjectRepository;
        $this->examRepository = $examRepository;
        $this->studentSuccessRateUseCase = $studentSuccessRateUseCase;
        $this->StudentOrderUseCase = $StudentOrderUseCase;
        $this->learningPerformance = $learningPerformance;
//        $this->middleware(ParentInRelationMiddleware::class)
//            ->only(['getStudentAllSubjectsPerformance', 'getStudentSubjectPerformance']);
        $this->requestLiveSessionUseCase = $requestLiveSessionUseCase;
    }

    private function getStudentOrderInSubject($studentId, $subjectId) {
        if(isset($studentId)){
            $res = $this->StudentOrderUseCase->getStudentOrderInSubject($subjectId);
            $order = array_search($studentId, array_keys($res));
        }
        return ($order + 1);
    }


    // unused function
    public function getStudentPerformance($studentId) {
        $this->learningPerformance->student_id = $studentId;
        return $this->transformDataModInclude($this->learningPerformance, '', new StudentPerformanceTransformer(), ResourceTypesEnums::LEARNING_PERFORMANCE);
    }

    public function getStudentSubjectPerformance($studentId, $subjectId)
    {
        $studentObj = $this->studentRepository->findOrFail($studentId);
        $this->learningPerformance->student = $studentObj->load('user');
        $this->learningPerformance->subject = $this->subjectRepository->findOrFail($subjectId);

        $this->learningPerformance->student_order =
            $this->getStudentOrderInSubject($studentId, $subjectId);
        $this->learningPerformance->success_rate =
            $this->studentSuccessRateUseCase
                ->getSuccessRateOnSubject($studentId, $subjectId);

        // speed_percentage order according to all students
        $this->learningPerformance->solving_speed_percentage_order =
            $this->studentSuccessRateUseCase
                ->getStudentSpeedOrderOfSolvingExams($studentId, $subjectId);

        // subject progress percentage order according to all students
        $this->learningPerformance->subject_progress_percentage_order =
            $this->studentSuccessRateUseCase
                ->getSubjectProgressPercentage($studentId, $subjectId);

        // exams count order according to all students
        $this->learningPerformance->exams_count_order =
            $this->studentSuccessRateUseCase
                ->getExamsCountsOrder($studentId, $subjectId);
        $subject = Subject::find($subjectId);
        if($studentObj->user) {
            $this->learningPerformance->course_sessions_count = $this->studentRepository->getStudentCourseSessions($studentObj->user,$subject);
            $this->learningPerformance->live_sessions_count = $this->studentRepository->getStudentLiveSessions($studentObj->user,$subject);
            $this->learningPerformance->requested_sessions_count = $this->studentRepository->getStudentRequestedSessions($studentObj->user,$subject);
            $this->learningPerformance->courses = $this->studentRepository->getStudentCourseAttendance($studentObj,$subject);
        }
        return $this->transformDataModInclude($this->learningPerformance, 'course_sessions.course',
            new StudentSubjectPerformanceTransformer(),
            ResourceTypesEnums::LEARNING_PERFORMANCE);

    }


    public function getExamPerformance($examId)
    {
        $exam = $this->examRepository->findOrFail($examId);
        $exam->vcrSpot = $this->requestLiveSessionUseCase->getAvailableVcrSpot($exam->subject_id);

        return $this->transformDataModInclude($exam, 'vcrSpot.subject,vcrSpot.instructor.user',
            new ExamPerformanceTransformer(),
            ResourceTypesEnums::EXAM_PERFORMANCE);
    }

    public function getStudentAllSubjectsPerformance($studentId)
    {
        $studentObj = $this->studentRepository->findOrFail($studentId);
        $this->learningPerformance->student = $studentObj->load('user');
        if($studentObj->user) {
            $this->learningPerformance->course_sessions_count = $this->studentRepository->getStudentCourseSessions($studentObj->user);
            $this->learningPerformance->live_sessions_count = $this->studentRepository->getStudentLiveSessions($studentObj->user);
            $this->learningPerformance->requested_sessions_count = $this->studentRepository->getStudentRequestedSessions($studentObj->user);
            $this->learningPerformance->courses = $this->studentRepository->getStudentCourseAttendance($studentObj);
        }
        return $this->transformDataModInclude($this->learningPerformance, '',
            new StudentAllSubjectsPerformanceTransformer($this->learningPerformance),
            ResourceTypesEnums::LEARNING_PERFORMANCE);
    }

    public function getStudentActivityLog(Student $student)
    {
        $activities = $student
            ->events()
            ->when(request('date_from'), function ($q)  {
                $q ->where('created_at' , '>=' , startOfDay(request('date_from')));
            })
            ->when(request('date_to'), function ($q)  {
                $q ->where('created_at' , '<=' , endOfDay(request('date_to')));
            })
            ->whereIn('event_properties->subject_attributes->subject_id',
                $student->subjects()->pluck('subject_id')->toArray()
            )
            ->latest()
            ->paginate(10);

        return $this->transformDataModInclude($activities, '',
            new ActivitiesLogTransformer(),
            ResourceTypesEnums::ACTIVITY_LOG);
    }

    public function getStudentPackages(Student $student)
    {
        $packages = Package::query()
            ->where('country_id', $student->user->country_id)
            ->where('educational_system_id', $student->educational_system_id)
            ->where('academical_years_id', $student->academical_year_id)
            ->where('grade_class_id', $student->class_id)
            ->paginate(10);

        return $this->transformDataModInclude(
            $packages,
            '',
            new PackagesListTransformer($student),
            ResourceTypesEnums::SUBJECT_PACKAGE
        );
    }

    public function getStudentSubjects(Student $student)
    {
        $subjects = Subject::query()
            ->where('country_id', $student->user->country_id)
            ->where('educational_system_id', $student->educational_system_id)
            ->where('academical_years_id', $student->academical_year_id)
            ->where('grade_class_id',  $student->class_id);
            if (request()->has('subscribed')) {
                if (request()->boolean('subscribed')) {
                    $subjects->whereHas('students', function ($q) use ($student) {
                        $q->where('student_id', "=", $student->id);
                    });
                } else {
                       $subjects->whereDoesntHave("students", function ($q) use ($student){
                        $q->where('student_id', "=", $student->id);
                    });
                }
            }
            $subjects = $subjects->paginate(10);

        return $this->transformDataModInclude(
            $subjects,
            '',
            new SubjectsListTransformer($student),
            ResourceTypesEnums::SUBJECT
        );
    }

    public function getStudentCourses(Student $student)
    {
        $subjects = Subject::query()
            ->where('academical_years_id', $student->academical_year_id)
            ->where('educational_system_id', $student->educational_system_id)
            ->where('country_id', $student->user->country_id)
            ->where('grade_class_id', $student->class_id)
            ->pluck('id')->toArray();

        $courses = Course::query()
            ->latest()
            ->where(function ($query) use ($subjects) {
                $query->whereIn('subject_id', $subjects);
                $query->orWhere('subject_id', null);
            })->where('end_date', '>', date("Y-m-d"))
            ->where('is_active',1)
            ->with(['sessions' =>  function ($query){
              $query->where('status','!=',CourseSessionEnums::CANCELED);
            }, 'instructor', 'subject']);

            if (request()->has('subscribed')) {
                if (request()->boolean('subscribed')) {
                     $courses->whereHas('students', function ($q) use ($student) {
                        $q->where('student_id', "=", $student->id);
                    });
                } else {
                    $subscripedCourses = (clone $courses)->whereHas('students', function ($q) use ($student) {
                        $q->where('student_id', '=', $student->id);
                    })->pluck('id')->toArray();
                    $courses->whereNotIn('id', $subscripedCourses
                );

                }
            }
        $courses = $courses->paginate(10);

        return $this->transformDataModInclude(
            $courses,
            '',
            new CoursesListTransformer($student),
            ResourceTypesEnums::COURSE
        );
    }
    public function getStudentUnsubscribedTopQudratCourses(Student $student)
    {
        $courses = Course::query()
            ->whereDoesntHave('students', function ($q) use ($student) {
                $q->where('student_id', '=', $student->id);
            })
            ->where('is_active',1)
            ->where('is_top_qudrat',1)
            ->with(['sessions' =>  function ($query){
              $query->where('status','!=',CourseSessionEnums::CANCELED);
            }, 'instructor', 'subject'])
            ->latest()
            ->paginate(10);


        return $this->transformDataModInclude(
            $courses,
            '',
            new CoursesListTransformer($student),
            ResourceTypesEnums::COURSE
        );
    }
    public function getStudentSubscribedCourses(Student $student)
    {
        $courses = Course::query()
            ->whereHas('students', function ($q) use ($student) {
                $q->where('student_id', '=', $student->id);
            })
            ->where('is_active',1)
            ->with(['sessions' =>  function ($query){
              $query->where('status','!=',CourseSessionEnums::CANCELED);
            }, 'instructor', 'subject'])
            ->latest()
            ->paginate(10);


        return $this->transformDataModInclude(
            $courses,
            '',
            new CoursesListTransformer($student),
            ResourceTypesEnums::COURSE
        );
    }

    public function getQudratStudentSubjects(Student $student)
    {
        $subjects = Subject::query()
            ->where('country_id', $student->user->country_id)
            ->where('educational_system_id', $student->educational_system_id)
            ->where('academical_years_id', $student->academical_year_id)
            ->where('is_top_qudrat', true);

        if (request()->has('subscribed')) {
                if (request()->boolean('subscribed')) {
                    $subjects->whereHas('students', function ($q) use ($student) {
                        $q->where('student_id', "=", $student->id);
                    });
                } else {
                       $subjects->whereDoesntHave("students", function ($q) use ($student){
                        $q->where('student_id', "=", $student->id);
                    });
                }
        }

        $subjects = $subjects->paginate(10);

        return $this->transformDataModInclude(
            $subjects,
            '',
            new SubjectsListTransformer($student),
            ResourceTypesEnums::SUBJECT
        );
    }
    public function getStudentSubscribedAndUnsubscribedTopQudratCourses(Student $student)
    {
        $courses = Course::query()
            ->where('end_date', '>', date("Y-m-d"))
            ->where('is_active',1)
            ->where('is_top_qudrat',1)
            ->with(['sessions' =>  function ($query){
                $query->where('status','!=',CourseSessionEnums::CANCELED);
            }, 'instructor', 'subject'])
            ->latest()
            ->paginate(10);


        return $this->transformDataModInclude(
            $courses,
            '',
            new CoursesListTransformer($student),
            ResourceTypesEnums::COURSE
        );
    }

}
