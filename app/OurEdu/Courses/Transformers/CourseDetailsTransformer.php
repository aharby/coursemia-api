<?php

namespace App\OurEdu\Courses\Transformers;

use App\OurEdu\Users\User;
use App\OurEdu\Courses\Models\Course;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
class CourseDetailsTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [
        'actions'
    ];

    public function __construct( public User $user)
    {

    }

    public function transform(Course $course)
    {
        $currency_code = $this->user->student->educationalSystem->country->currency ?? '';
        $transformedData = [
            'id' => (int) $course->id,
            'name' => (string) $course->name,
            'start_date' => (string) $course->start_date,
            'end_date' => (string) $course->end_date,
            'picture' => (string) imageProfileApi($course->picture, 'large'),
            'subscription_cost' =>(float) $course->subscription_cost . " " . $currency_code,
            'average_rating' => (string) $course->avgRating(),
        ];


         return $transformedData;
    }

    public function includeActions(Course $course)
    {
        $actions = [];

        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.student.courses.subscribe', ['courseId' => $course->id]),
            'label' => trans('app.Subscribe'),
            'method' => 'POST',
            'key' => APIActionsEnums::COURSE_SUBSCRIBE
        ];

         $actions[] = [
        'endpoint_url' => buildScopeRoute('api.student.courses.show', ['id' => $course->id]),
        'label' => trans('app.View'),
        'method' => 'GET',
        'key' => APIActionsEnums::VIEW_COURSE
        ];

    return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
    }

}
