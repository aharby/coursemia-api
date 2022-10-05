<?php


namespace App\Modules\StaticPages\Transformers;

use App\Modules\Users\User;
use League\Fractal\TransformerAbstract;
use App\Modules\BaseApp\Enums\ResourceTypesEnums;
use App\Modules\BaseApp\Api\Enums\APIActionsEnums;
use App\Modules\BaseApp\Api\Transformers\ActionTransformer;

class ListInstructorsTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];
    protected array $availableIncludes = [
        'actions'
    ];


    public function transform(User $instructor)
    {
        return [
            'id' => (int) $instructor->id,
            'name' => (string) $instructor->name,
            'profile_picture' => (string) imageProfileApi($instructor->profile_picture),
            'average_rating' => (string) $instructor->avgRating(),
            'reviews' => (int) $instructor->ratings()->count()
        ];
    }

    public function includeActions(User $instructor)
    {
        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.show-instructor', [
                'instructor' => $instructor->id,
            ]),
            'label' => trans('app.view'),
            'method' => 'GET',
            'key' => APIActionsEnums::VIEW_INSTRUCTOR
        ];

        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }
}

