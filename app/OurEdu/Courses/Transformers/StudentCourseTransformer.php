<?php

namespace App\OurEdu\Courses\Transformers;

use Carbon\Carbon;
use App\OurEdu\Users\UserEnums;
use Illuminate\Support\Facades\DB;
use \Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use App\OurEdu\Courses\Models\Course;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Courses\Enums\CourseSessionEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\Users\Transformers\UserTransformer;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudent;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\Subjects\SME\Transformers\SubjectTransformer;
use App\OurEdu\Courses\Transformers\CourseRatingsTransformer;
use App\OurEdu\Courses\Transformers\CourseSessionTransformer;
use App\OurEdu\Courses\Transformers\CourseHomeWorkTransformer;
use App\OurEdu\Courses\Transformers\CourseRatingDetailsTransformer;
use App\OurEdu\Courses\Models\SubModels\CourseSession;
use OwenIt\Auditing\Models\Audit;

class StudentCourseTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'sessions',
        'instructor',
        'subject',
        'ratings',
        'ratingDetails',
        'actions'
    ];

    protected array $availableIncludes = [
        'homeworks'
    ];

    protected $user;

    public function __construct($user = null)
    {
        $this->user = $user;
    }

    public function transform(Course $course)
    {
        $currency_code = $this->user->student->educationalSystem->country->currency ?? '';
        return [
            'id' => (int)$course->id,
            'name' => (string)$course->name,
            'description' => (string)$course->description,
            'course-type' => (string)$course->type,
            'instructor_id' => (int)$course->instructor_id,
            'subject_id' => (int)$course->subject_id,
            'subscription_cost' => (string)$course->subscription_cost . ' ' . $currency_code,
            'subscription_amount' => (string)$course->subscription_cost,
            'start_date' => (string)$course->start_date,
            'end_date' => (string)$course->end_date,
            'picture' => imageProfileApi($course->picture, 'large'),
            'progress' => calculateCourseProgress($course),
            'is_active' => (bool)$course->is_active,
            'number_of_sessions' => $course->sessions()
                ->where('status', '!=', CourseSessionEnums::CANCELED)
                ->count(),
            'is_subscribe' => is_student_subscribed_to_course($course, $this->user),
            'students_count' => $course->students->count(),
            'number_of_attended_sessions' => calculateAttendanceSessions($course),
            'updated_at' => $this->lastUpdatedAtEventOnCourse($course) ? (string) $this->lastUpdatedAtEventOnCourse($course)->updated_at : '',
            'medium_picture' => (string) imageProfileApi($course->medium_picture, 'large'),
            'small_picture' => (string) imageProfileApi($course->small_picture, 'large'),
            'apple_price' => $course->apple_price . ' ' . $currency_code
        ];
    }

    public function includeActions(Course $course)
    {
        $actions = [];

        if ($authUser = Auth::guard('api')->user()) {
            // parent case
            if ($authUser->type == UserEnums::PARENT_TYPE && $student = $this->user->student) {
                $userIsSubscriped = DB::table('course_student')
                    ->where('course_id', $course->id)
                    ->where('student_id', $student->id)
                    ->exists();

                if (!$userIsSubscriped) {
                    $actions[] = [
                        'endpoint_url' => buildScopeRoute('api.parent.subscriptions.post.courseSubscripe', [
                            'id' => $course->id,
                            'studentId' => $student->id
                        ]),
                        'label' => trans('app.Subscribe'),
                        'method' => 'POST',
                        'key' => APIActionsEnums::COURSE_SUBSCRIBE
                    ];
                }

                if (!$userIsSubscriped && $student->wallet_amount < $course->subscription_cost) {
                    $actions[] = [
                        'endpoint_url' => buildScopeRoute(
                            'api.parent.payments.submitTransaction',
                            ['student_id' => $this->user->student->id]
                        ),
                        'label' => trans('subscriptions.Add money'),
                        'method' => 'POST',
                        'key' => APIActionsEnums::ADD_MONEY_TO_WALLET
                    ];
                }
            }

            // student case
            if ($authUser->type == UserEnums::STUDENT_TYPE && $student = $authUser->student) {
                $userIsSubscriped = DB::table('course_student')
                    ->where('course_id', $course->id)
                    ->where('student_id', $student->id)
                    ->exists();

                if (!$userIsSubscriped) {
                    $actions[] = [
                        'endpoint_url' => buildScopeRoute('api.student.courses.subscribe', ['courseId' => $course->id]),
                        'label' => trans('app.Subscribe'),
                        'method' => 'POST',
                        'key' => APIActionsEnums::COURSE_SUBSCRIBE
                    ];
                }


                $firstSession = $course->VCRSession()
                    ->where('time_to_end', '<', now())
                    ->first();

                $ratedBefore = $course->ratings()->where('user_id', $authUser->id)->exists();
                if ($userIsSubscriped && isset($firstSession) && !$ratedBefore) {
                    $actions[] = [
                        'endpoint_url' => buildScopeRoute(
                            'api.student.courses.rateCourse',
                            ['courseId' => $course->id]
                        ),
                        'label' => trans('course.rate'),
                        'method' => 'POST',
                        'key' => APIActionsEnums::COURSE_RATE
                    ];
                }
            }
        }

        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.show-instructor', ['instructor' => $course->instructor_id]),
            'label' => trans('app.Instructor'),
            'method' => 'GET',
            'key' => APIActionsEnums::VIEW_INSTRUCTOR_PROFILE
        ];

        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.discussions.index', ['course' => $course->id]),
            'label' => trans('courses.discussion.list discussions'),
            'key' => APIActionsEnums::LIST_COURSE_DISCUSSIONS,
            'method' => 'POST'
        ];

        return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
    }

    public function includeInstructor(Course $course)
    {
        if ($course->instructor) {
            return $this->item($course->instructor, new UserTransformer(), ResourceTypesEnums::USER);
        }
    }

    public function includeSubject(Course $course)
    {
        if ($course->subject) {
            return $this->item($course->subject, new SubjectTransformer(), ResourceTypesEnums::SUBJECT);
        }
    }

    public function includeSessions(Course $course)
    {
        $sessions = $course->sessions()->with('VCRSession')
            ->where('status', '!=', CourseSessionEnums::CANCELED)->get();
        $params['user'] = $this->user;
        $params['userIsSubscribed'] = DB::table('course_student')
            ->where('course_id', $course->id)
            ->where(
                'student_id',
                isset($this->user) ? $this->user?->student?->id : Auth::guard('api')->user()->student?->id
            )
            ->exists();
        return $this->collection($sessions, new CourseSessionTransformer($params), ResourceTypesEnums::COURSE_SESSION);
    }

    public function includeRatings(Course $course)
    {
        return $this->item($course, new CourseRatingsTransformer(), ResourceTypesEnums::RATING);
    }

    public function includeRatingDetails(Course $course)
    {
        if ($course->ratings()->count() > 0) {
            $rating = $course->ratings()->latest()->get();

            return $this->collection($rating, new CourseRatingDetailsTransformer(), ResourceTypesEnums::RATING_DETAILS);
        }
    }

    public function includeHomeworks(Course $course)
    {
        $student = $course->students()?->where('id', $this->user->student->id)->first();
        if ($student) {
            $courseHomeworks = $course->homeworks()
                ->whereNotNull('published_at')
                ->where('start_at', '<=', now())
                ->where('end_at', '>', now())
                ->active()
                ->get();

            return $this->collection($courseHomeworks, new CourseHomeWorkTransformer(), ResourceTypesEnums::HOMEWORK);
        }
    }

    private function lastUpdatedAtEventOnCourse(Course $course)
    {
        $sessionsIdOnCourse = $course->sessions()->pluck('id')->toArray();
        $lastUpdateEvent = Audit::whereIn(
            'auditable_type',
            [Course::class, CourseSession::class, 'course', 'courseSession']
        )
            ->select('updated_at')
            ->where('event', '=', 'updated')
            ->where('auditable_id', $course->id)
            ->orWhereIn('auditable_id', $sessionsIdOnCourse)
            ->latest()->first();

        return $lastUpdateEvent;
    }
}
