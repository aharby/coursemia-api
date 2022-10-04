<?php

namespace App\OurEdu\Courses\Student\Controllers;

use App\Exceptions\OurEduErrorException;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Courses\Repository\CourseRepositoryInterface;
use App\OurEdu\Courses\Repository\CourseSessionRepositoryInterface;
use App\OurEdu\Courses\Student\Requests\RateCourseRequest;
use App\OurEdu\Courses\Transformers\CourseListTransformer;
use App\OurEdu\Courses\Transformers\CourseSessionListTransformer;
use App\OurEdu\Courses\Transformers\CourseTransformer;
use App\OurEdu\Courses\Transformers\StudentCourseTransformer;
use App\OurEdu\Courses\Transformers\ViewCourseSessionTransformer;
use App\OurEdu\Courses\UseCases\CourseRateUseCase\CourseRateUseCaseInterface;
use App\OurEdu\Courses\UseCases\CourseSubscribeUseCase\CourseSubscribeUseCaseInterface;
use App\OurEdu\Users\Repository\StudentRepositoryInterface;
use App\OurEdu\Users\Repository\UserRepositoryInterface;
use App\OurEdu\Users\Transformers\UserTransformer;
use App\OurEdu\Users\UserEnums;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Swis\JsonApi\Client\Interfaces\ParserInterface;

class CourseApiController extends BaseApiController
{
    /**
     * @var \App\OurEdu\Courses\UseCases\CourseSubscribeUseCase\CourseSubscribeUseCaseInterface
     */
    private $courseSubscribeUseCase;
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;
    /**
     * @var CourseRepositoryInterface
     */
    private $courseRepository;
    /**
     * @var StudentRepositoryInterface
     */
    private $studentRepository;

    private $parserInterface;
    private $courseRateUseCase;
    private $CourseSessionRepository;

    public function __construct(
        CourseRepositoryInterface $courseRepository,
        UserRepositoryInterface $userRepository,
        CourseSubscribeUseCaseInterface $courseSubscribeUseCase,
        StudentRepositoryInterface $studentRepository,
        ParserInterface $parserInterface,
        CourseRateUseCaseInterface $courseRateUseCase,
        CourseSessionRepositoryInterface $CourseSessionRepository
    ) {
        $this->courseRepository = $courseRepository;
        $this->userRepository = $userRepository;
        $this->courseSubscribeUseCase = $courseSubscribeUseCase;
        $this->studentRepository = $studentRepository;
        $this->parserInterface = $parserInterface;
        $this->courseRateUseCase = $courseRateUseCase;
        $this->CourseSessionRepository = $CourseSessionRepository;

        $this->user = Auth::guard('api')->user();
        $this->middleware('type:student')->only('listCourses');
        $this->middleware('type:student')->only('listSessionCourses');
//        $this->middleware(AvailableCourseMiddleware::class)->only('subscribe');
    }

    /*
     * List Courses of student for parent
     */
    public function listCoursesForStudent($student)
    {

        $student = $this->studentRepository->findOrFail($student);
        $courses = $this->courseRepository->getCoursesRelatedToStudent($student);
        return $this->transformDataModInclude(
            $courses,
            ['instructor', 'sessions', 'subject', 'actions','homeworks'],
            new CourseListTransformer($student->user),
            ResourceTypesEnums::COURSE
        );
    }

    public function listSessionCourses()
    {
        $student = auth()->user()->student;
        $sessions = $this->CourseSessionRepository->getRelatedSessionForStudent($student);

        return $this->transformDataModInclude(
            $sessions,'',
            new CourseSessionListTransformer(),
            ResourceTypesEnums::COURSE_SESSION
        );
    }

    public function show($id)
    {
        $course = $this->courseRepository->findOrFail($id);

        $user = request('user_id') ? $this->userRepository->findOrFail(request('user_id')) : $this->user;
        $course->load('sessions', 'instructor', 'subject');
         if (auth()->user()->type === UserEnums::STUDENT_TYPE){
            return $this->transformDataModInclude(
                $course,
                ['instructor', 'sessions.recordedSessions', 'subject', 'actions','sessions.actions' , 'homeworks'],
                new StudentCourseTransformer($user),
                ResourceTypesEnums::COURSE
            );
         }

        return $this->transformDataModInclude(
            $course,
            ['instructor', 'sessions', 'subject', 'actions','sessions.actions' , 'homeworks'],
            new CourseTransformer($user),
            ResourceTypesEnums::COURSE
        );
    }
    // Student subscribe to a course
    public function subscribe($courseId)
    {
        try {
            $studentId = auth()->user()->student->id;
            $subscribe = $this->courseSubscribeUseCase->subscribeCourse($courseId, $studentId);
            if ($subscribe['status'] == 200) {
                return response()->json([
                    'meta' => [
                        'message' => trans('app.Subscribed Successfully')
                    ]
                ]);
            } else {
                return formatErrorValidation($subscribe);
            }
        } catch (\Throwable $exception) {
            Log::error($exception);
            throw new OurEduErrorException($exception->getMessage());
        }
    }

    /*
     * List Courses for authenticated student
     */
    public function listCourses()
    {
        $student = auth()->user()->student;
        $courses = $this->courseRepository->getAllStudentCourses($student);

        return $this->transformDataModInclude(
            $courses,
            ['instructor', 'sessions', 'subject', 'actions','homeworks'],
            new CourseListTransformer($student->user),
            ResourceTypesEnums::COURSE
        );
    }

    public function instructorProfile($id)
    {
        $instructor = $this->userRepository->findOrFail($id);

        return $this->transformDataModInclude(
            $instructor,
            ['ratings.user'],
            new UserTransformer(),
            ResourceTypesEnums::USER
        );
    }


    public function rateCourse(RateCourseRequest $request, $courseId)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();
        try {
            $rate = $this->courseRateUseCase->rateCourse($data, $courseId, $this->user);
            if ($rate['status'] == 200) {
                return response()->json([
                    'meta' => [
                        'message' => trans('api.Thanks for rating')
                    ]
                ]);
            } else {
                return formatErrorValidation($rate);
            }
        } catch (\Throwable $exception) {
            Log::error($exception);
            throw new OurEduErrorException($exception->getMessage());
        }
    }

    // Student view course session
    public function courseSession($id)
    {
        $course = $this->CourseSessionRepository->findOrFail($id);

        return $this->transformDataModInclude(
            $course, [],new ViewCourseSessionTransformer(),
            ResourceTypesEnums::COURSE_SESSION
        );
    }

    public function listUnsubscribedCourses()
    {
        $student = auth()->user()->student;
        $courses = $this->courseRepository->getStudentUnsubscribedCourses($student);

        return $this->transformDataModInclude(
            $courses,
            ['instructor', 'sessions', 'subject', 'actions','homeworks'],
            new CourseListTransformer($student->user),
            ResourceTypesEnums::COURSE
        );
    }
    public function listsubScribedAndUnsubscribedCourses()
    {
        $student = auth()->user()->student;
        $courses = $this->courseRepository->getStudentSubscribedAndUnsubscribedCourses();

        return $this->transformDataModInclude(
            $courses,
            ['instructor', 'sessions', 'subject', 'actions','homeworks'],
            new CourseListTransformer($student->user),
            ResourceTypesEnums::COURSE
        );
    }
    public function listSubscribedCourses()
    {
        $student = auth()->user()->student;
        $courses = $this->courseRepository->getStudentSubscribedCourses($student);

        return $this->transformDataModInclude(
            $courses,
            ['instructor', 'sessions', 'subject', 'actions','homeworks'],
            new CourseListTransformer($student->user),
            ResourceTypesEnums::COURSE
        );
    }

}
