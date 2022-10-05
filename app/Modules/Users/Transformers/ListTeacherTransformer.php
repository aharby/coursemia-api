<?php

namespace App\Modules\Users\Transformers;

use App\Modules\Users\User;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;
use App\Modules\BaseApp\Enums\ResourceTypesEnums;
use App\Modules\BaseApp\Api\Enums\APIActionsEnums;
use App\Modules\Users\Transformers\UserTransformer;
use App\Modules\BaseApp\Api\Transformers\ActionTransformer;

class ListTeacherTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions'
    ];

    protected array $availableIncludes = [
        'students'
    ];

    public function transform(User $user)
    {
        $transformedData = [
            'id' => $user->id,
            'name' => (string)$user->first_name . (string)$user->last_name,
            'profile_picture' => (string)imageProfileApi($user->profile_picture),
        ];
        return $transformedData;
    }

    public function includeActions($user)
    {
        $actions = [];

        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.profile.removeRelation', ['id' => $user->id]),
            'label' => trans('invitations.Remove Teacher'),
            'method' => 'GET',
            'key' => APIActionsEnums::REMOVE_TEACHER
        ];


        return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
    }

    public function includeStudents($user)
    {
        $supervisedStudents = $user->supervisedStudents;
        return $this->collection($supervisedStudents, new UserTransformer([]), ResourceTypesEnums::USER);
    }
}
