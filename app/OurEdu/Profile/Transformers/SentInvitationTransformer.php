<?php

namespace App\OurEdu\Profile\Transformers;

use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;
use App\OurEdu\Invitations\Models\Invitation;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\Invitations\Enums\InvitationEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\Subjects\Student\Transformers\ListSubjectsTransformer;

class SentInvitationTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions',
        'subjects',
        'receiver',
    ];

    protected array $availableIncludes = [
    ];

    private $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform(Invitation $invitation)
    {
        $transformedData = [
            'id' => (int) $invitation->id,
            'status' => $invitation->status ?? InvitationEnums::PENDING,
            'receiver_email' => (string) $invitation->receiver_email,
        ];

        return $transformedData;
    }

    public function includeActions(Invitation $invitation)
    {
        $actions = [];

        if ($user = Auth::guard('api')->user()) {
            if ($invitation->status == InvitationEnums::PENDING) {
                $actions[] = [
                    'endpoint_url' => buildScopeRoute('api.invitations.cancelInviation', ['id' => $invitation->id]),
                    'label' => trans('invitations.Cancel Invitation'),
                    'method' => 'POST',
                    'key' => APIActionsEnums::CANCEL_INVITATION
                ];

                $actions[] = [
                    'endpoint_url' => buildScopeRoute('api.invitations.resendInvite', ['id' => $invitation->id]),
                    'label' => trans('invitations.Resend Invitation'),
                    'method' => 'POST',
                    'key' => APIActionsEnums::RESEND_INVITATION
                ];
            }
        }

        if (count($actions) > 0) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }

    public function includeSubjects($invitation)
    {
        if ($invitation->subjects()->count()) {
            return $this->collection($invitation->subjects, new ListSubjectsTransformer(), ResourceTypesEnums::SUBJECT);
        }
    }

    public function includeReceiver(Invitation $invitation)
    {
        return $this->item($invitation, new InvitationReceiverTransformer(), ResourceTypesEnums::USER);
    }
}
