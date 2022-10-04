<?php

namespace App\OurEdu\Invitations\Transformers;

use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;
use App\OurEdu\Invitations\Models\Invitation;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\Invitations\Enums\InvitationEnums;
use App\OurEdu\Users\Transformers\UserTransformer;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\Subjects\Student\Transformers\ListSubjectsTransformer;

class InvitationTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions',
    ];

    protected array $availableIncludes = [
        'sender',
        'receiver',
//        'invitable',
        'subjects',
    ];

    private $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform(Invitation $invitation)
    {
        $transformerDatat = [
            'id' => (int) $invitation->id,
            'sender_id' => (int) $invitation->sender_id,
            'status' => $invitation->status ?? InvitationEnums::PENDING,
            'receiver_email' => (string) $invitation->receiver_email,
        ];

        return $transformerDatat;
    }

    public function includeSender(Invitation $invitation)
    {
        if ($invitation->sender) {
            return $this->item($invitation->sender, new UserTransformer($this->params), ResourceTypesEnums::USER);
        }
    }

    public function includeReceiver(Invitation $invitation)
    {
        if ($invitation->receiver()->exists()) {
            return $this->item($invitation->receiver, new UserTransformer($this->params), ResourceTypesEnums::USER);
        }
    }

    public function includeSubjects($invitation)
    {
        if ($invitation->subjects->count()) {
            return $this->collection($invitation->subjects, new ListSubjectsTransformer(), ResourceTypesEnums::SUBJECT);
        }
    }

    public function includeActions(Invitation $invitation)
    {
        $actions = [];

        if ($user = Auth::guard('api')->user()) {

            // case logged user is the sender
            if ($user->id == $invitation->sender_id) {
                if ($invitation->status == InvitationEnums::PENDING) {
                    // cancel action
                    $actions[] = [
                        'endpoint_url' => buildScopeRoute('api.invitations.cancelInviation', ['id' => $invitation->id]),
                        'label' => trans('invitations.Cancel Invitation'),
                        'method' => 'GET',
                        'key' => APIActionsEnums::CANCEL_INVITATION
                    ];


                    // resend invitaion action
                    // the student doesnt have a parent
                    if ($user->type == UserEnums::STUDENT_TYPE) {
                        if (! $user->parents()->where('email', $invitation->receiver_email)->exists()) {
                            $actions[] = [
                                'endpoint_url' => buildScopeRoute('api.invitations.resendInvite', ['id' => $invitation->id]),
                                'label' => trans('invitations.Resend Invitation'),
                                'method' => 'POST',
                                'key' => APIActionsEnums::RESEND_INVITATION
                            ];
                        }
                    }

                    // the parent doesnt have a student
                    if ($user->type == UserEnums::PARENT_TYPE) {
//                        if (! $user->students()->where('email', $invitation->receiver_email)->exists()) {
                        if (! $user->students()->get()->where('user.email', $invitation->receiver_email)->first()) {
                            $actions[] = [
                                'endpoint_url' => buildScopeRoute('api.invitations.resendInvite', ['id' => $invitation->id]),
                                'label' => trans('invitations.Resend Invitation'),
                                'method' => 'POST',
                                'key' => APIActionsEnums::RESEND_INVITATION
                            ];
                        }
                    }
                }
            }

            // case logged user is the receiver
            if ($user->email == $invitation->receiver_email) {
                if ($invitation->status == InvitationEnums::PENDING) {
                    // accept action
                    $actions[] = [
                        'endpoint_url' => buildScopeRoute('api.invitations.changeStatus', ['id' => $invitation->id, 'status' => InvitationEnums::ACCEPTED]),
                        'label' => trans('invitations.Accept Invitation'),
                        'method' => 'GET',
                        'key' => APIActionsEnums::ACCEPT_INVITATION
                    ];

                    // refuse action
                    $actions[] = [
                        'endpoint_url' => buildScopeRoute('api.invitations.changeStatus', ['id' => $invitation->id, 'status' => InvitationEnums::REFUSED]),
                        'label' => trans('invitations.Refuse Invitation'),
                        'method' => 'GET',
                        'key' => APIActionsEnums::REFUSE_INVITATION
                    ];
                }
            }
        }

        if (count($actions) > 0) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }
}
