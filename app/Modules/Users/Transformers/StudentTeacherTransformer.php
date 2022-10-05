<?php

namespace App\Modules\Users\Transformers;

use App\Modules\Users\Transformers\ListChildrenTransformer;
use App\Modules\Users\User;
use App\Modules\Users\UserEnums;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;
use App\Modules\BaseApp\Enums\ResourceTypesEnums;
use App\Modules\BaseApp\Api\Enums\APIActionsEnums;
use App\Modules\Invitations\Enums\InvitationEnums;
use App\Modules\BaseApp\Api\Transformers\ActionTransformer;
use App\Modules\Profile\Transformers\SentInvitationTransformer;
use App\Modules\Profile\Transformers\ReceivedInvitationTransformer;

class StudentTeacherTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions'
    ];

    protected array $availableIncludes = [
        'students',
        'user',
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

    public function includeStudents($user)
    {
        if ($user->supervisedStudents->count()) {
            $students = $user->supervisedStudents()->with('student')->get();
            return $this->collection($students, new ListChildrenTransformer(), ResourceTypesEnums::STUDENT);
        }
    }

    public function includeActions(User $parent)
    {
        if (! isset($this->params['no_action'])) {
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
                }
            }
        }
        if (count($actions) > 0) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }

    public function includeSentInvitation()
    {
        $invitations = Auth::guard('api')->user()->sentInvitations()
            ->whereIn('status', InvitationEnums::getSenderAvailableStatuses())
            ->get();
        // returning the invitations except the ACCEPTED one's (because they already related)
        if (isset($invitations) && count($invitations) > 0) {
            return $this->collection($invitations, new SentInvitationTransformer(), ResourceTypesEnums::INVITATION);
        }
    }

    public function includeReceivedInvitations()
    {
        $invitations = Auth::guard('api')->user()->receivedInvitations()
            ->whereIn('status', InvitationEnums::getReceiverAvailableStatuses())
            ->get();

        if (isset($invitations) && count($invitations) > 0) {
            return $this->collection($invitations, new ReceivedInvitationTransformer(), ResourceTypesEnums::INVITATION);
        }
    }

    public function includeUser($teacher)
    {
        return $this->item($teacher->user, new UserTransformer(), ResourceTypesEnums::USER);
    }
}
