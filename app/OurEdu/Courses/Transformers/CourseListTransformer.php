<?php

namespace App\OurEdu\Courses\Transformers;

use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\OurEdu\Courses\Models\Course;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\Users\Transformers\UserTransformer;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\Subjects\SME\Transformers\SubjectTransformer;
use App\OurEdu\Courses\Transformers\CourseSessionTransformer;
use App\OurEdu\Courses\Enums\CourseSessionEnums;
use App\OurEdu\Courses\Models\SubModels\CourseSession;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudent;

class CourseListTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions',
        'instructor',
        'ratings',
        'ratingDetails',
    ];
    protected array $availableIncludes = [
        'sessions',
        'subject',
        'homeworks'
    ];

    protected $user;

    public function __construct($user = null)
    {
        $this->user = $user ?? new User();
    }

    public function transform(Course $course)
    {
        $currencyCode = $this->user->student->educationalSystem->country->currency ?? '';

        return [
            'id' => (int)$course->id,
            'name' => (string)$course->name,
            'truncate_name' => (string)(truncateString($course->name, 10)),
            'description' => (string)$course->description,
            'course-type' => (string)$course->type,
            'instructor_id' => (int)$course->instructor_id,
            'subject_id' => (int)$course->subject_id,
            'subscription_cost' => (float)$course->subscription_cost . " " . $currencyCode,
            'subscription_amount' => (float)$course->subscription_cost,
            'start_date' => (string)$course->start_date,
            'end_date' => (string)$course->end_date,
            'is_active' => (bool)$course->is_active,
            'number_of_sessions' => $course->sessions()
                ->where('status', '!=', CourseSessionEnums::CANCELED)
                ->count(),
            'number_of_ended_sessions' => $this->getSessionTimeLeft($course),
            'progress'=>calculateCourseProgress($course),
            'picture' => (string) imageProfileApi($course->picture,'large'),
            'is_subscribe' =>  is_student_subscribed_to_course($course , $this->user),
            'number_of_attended_sessions' => calculateAttendanceSessions($course),
            'medium_picture' => (string) imageProfileApi($course->medium_picture, 'large'),
            'small_picture' => (string) imageProfileApi($course->small_picture, 'large'),
            'apple_price' => $course->apple_price. ' ' . $currencyCode
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
                        'endpoint_url' => buildScopeRoute(
                            'api.parent.subscriptions.post.courseSubscripe',
                            ['id' => $course->id, 'studentId' => $student->id]
                        ),
                        'label' => trans('app.Subscribe'),
                        'method' => 'POST',
                        'key' => APIActionsEnums::COURSE_SUBSCRIBE
                    ];
                }

                if (!$userIsSubscriped && $student->wallet_amount < $course->subscription_cost) {
                    $actions[] = [
                        'endpoint_url' => buildScopeRoute('api.parent.payments.submitTransaction', ['student_id' => $this->user->student->id]),
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
                        'endpoint_url' => buildScopeRoute('api.student.courses.subscribe', ['courseId' => $course]),
                        'label' => trans('app.Subscribe'),
                        'method' => 'POST',
                        'key' => APIActionsEnums::COURSE_SUBSCRIBE
                    ];
                }
                $actions[] = [
                    'endpoint_url' => buildScopeRoute('api.student.courses.show', ['id' => $course->id]),
                    'label' => trans('app.View'),
                    'method' => 'GET',
                    'key' => APIActionsEnums::VIEW_COURSE
                ];
            }
        }

        // parent and student teacher types => add user_id for the
        if ($authUser->type == UserEnums::STUDENT_TEACHER_TYPE || $authUser->type == UserEnums::PARENT_TYPE) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute(
                    'api.student.courses.show',
                    ['id' => $course->id, 'user_id' => $this->user->id]
                ),
                'label' => trans('app.View'),
                'method' => 'GET',
                'key' => APIActionsEnums::VIEW_COURSE
            ];
        }

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
        return $this->collection($course->sessions, new CourseSessionTransformer(), ResourceTypesEnums::COURSE_SESSION);
    }


    public function includeRatings(Course $course)
    {
        return $this->item($course, new CourseRatingsTransformer(), ResourceTypesEnums::RATING);
    }

    public function includeRatingDetails(Course $course)
    {
        if ($course->ratings()->count() > 0) {
            $ratings = $course->ratings()->inRandomOrder()->get();
            return $this->collection(
                $ratings,
                new CourseRatingDetailsTransformer(),
                ResourceTypesEnums::RATING_DETAILS
            );
        }
    }

    private function getSessionTimeLeft($course)
    {
        $counLeftTimeSessions = $course->sessions()->get()->filter(function ($item) {
            return $item->sessionEndTime <= now();
        })->count();

        return $counLeftTimeSessions;
    }

    public function includeHomeworks(Course $course)
    {
        $courseHomeworks = $course->homeworks()
            ->whereNotNull('published_at')
            ->where('start_at', '<=', now())
            ->where('end_at', '>', now())
            ->active()
            ->get();

        return $this->collection($courseHomeworks, new CourseHomeWorkTransformer(), ResourceTypesEnums::HOMEWORK);
    }
}
