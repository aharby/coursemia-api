<?php

namespace App\OurEdu\Users\Transformers;

use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\Invitations\Enums\InvitationEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\Profile\Transformers\InvitedParentsTransformer;
use App\OurEdu\Profile\Transformers\SentInvitationTransformer;
use App\OurEdu\Profile\Transformers\ReceivedInvitationTransformer;

class ParentTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions'
    ];

    protected array $availableIncludes = [
        'children',
        'sentInvitation',
        'receivedInvitations',
    ];

    private $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform(User $user)
    {
        if (isset($this->params['no_data'])) {
            $transformedData = [
                'id' => (int)$user->id,
            ];
            return $transformedData;
        }

        $transformedData = [
            'id' => (int)$user->id,
            'first_name' => (string)$user->first_name,
            'last_name' => (string)$user->last_name,
            'language' => (string)$user->language,
            'mobile' => (string)$user->mobile,
            'profile_picture' => (string)imageProfileApi($user->profile_picture),
            'type' => (string)$user->type,
            'email' => (string)$user->email,
            'country_id' => $user->country_id,
        ];
        return $transformedData;
    }

    public function includeChildren($user)
    {
        if ($user->students && count($user->students) > 0) {
            return $this->collection($user->students, new ListChildrenTransformer(), ResourceTypesEnums::STUDENT);
        }
    }

    public function includeActions(User $parent)
    {
        if (!isset($this->params['no_action'])) {
            $actions = [];

            if ($user = Auth::guard('api')->user()) {
                if ($user->type == UserEnums::STUDENT_TYPE) {
                    // remove the relation with this parent
                    $actions[] = [
                        'endpoint_url' => buildScopeRoute('api.profile.removeRelation', ['id' => $parent->id]),
                        'label' => trans('profile.Remove Student'),
                        'method' => 'GET',
                        'key' => APIActionsEnums::REMOVE_STUDENT
                    ];
                    // add another parent
                }
            }


            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.invitations.search'),
                'label' => trans('invitations.Search For Student'),
                'method' => 'GET',
                'key' => APIActionsEnums::ADD_STUDENT
            ];
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }

    public function includeSentInvitation()
    {
        $invitations = Auth::guard('api')->user()->sentInvitations()
            ->whereIn('status', InvitationEnums::getSenderAvailableStatuses())
            ->get();
        // returning the invitations except the ACCEPTED one's (because they already parents)
        if (isset($invitations) && count($invitations) > 0) {
            return $this->collection($invitations, new SentInvitationTransformer(), ResourceTypesEnums::INVITATION);
        }
    }

    public function includeReceivedInvitations()
    {
        $invitations = Auth::guard('api')->user()->receivedInvitations()
            ->whereHas('sender',function($query){
                $query->whereNull('deleted_at');
            })
            ->whereIn('status', InvitationEnums::getReceiverAvailableStatuses())
            ->get();

        if (isset($invitations) && count($invitations) > 0) {
            return $this->collection($invitations, new ReceivedInvitationTransformer(), ResourceTypesEnums::INVITATION);
        }
    }
}
