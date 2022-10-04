<?php

namespace App\OurEdu\Profile\Transformers;

use App\OurEdu\Users\User;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;
use App\OurEdu\Invitations\Models\Invitation;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\Invitations\Enums\InvitationEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\Subjects\Student\Transformers\ListSubjectsTransformer;

class ReceivedInvitationTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions',
        'subjects',
        'sender',
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
            'sender_email' => (string) $invitation->sender->email,
        ];

        return $transformedData;
    }

    public function includeActions(Invitation $invitation)
    {
        $actions = [];

        if ($user = Auth::guard('api')->user()) {
            if ($invitation->status == InvitationEnums::PENDING) {
                $actions[] = [
                    'endpoint_url' => buildScopeRoute('api.invitations.changeStatus', ['id' => $invitation->id, 'status' => InvitationEnums::ACCEPTED]),
                    'label' => trans('invitations.Accept Invitation'),
                    'method' => 'POST',
                    'key' => APIActionsEnums::ACCEPT_INVITATION
                ];

                $actions[] = [
                    'endpoint_url' => buildScopeRoute('api.invitations.changeStatus', ['id' => $invitation->id, 'status' => InvitationEnums::REFUSED]),
                    'label' => trans('invitations.Refuse Invitation'),
                    'method' => 'POST',
                    'key' => APIActionsEnums::REFUSE_INVITATION
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
            return $this->collection($invitation->subjects, new ListSubjectsTransformer([], $invitation->sender), ResourceTypesEnums::SUBJECT);
        }
    }

    public function includeSender(Invitation $invitation)
    {
        if ($invitation->sender()->exists()) {
            return $this->item($invitation->sender, new InvitationSenderTransformer(),
                ResourceTypesEnums::USER);
        }
    }
}
