<?php

namespace App\OurEdu\VCRSessions\Instructor\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use League\Fractal\TransformerAbstract;

class VCRSessionTransformer extends TransformerAbstract
{
    protected $params;

    protected array $defaultIncludes = [
        'actions'
    ];

    protected array $availableIncludes = [
    ];

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform($vcrSession)
    {
        $transformerData = [
            'id' => $vcrSession->id,
            'vcr_session_type' => $vcrSession->vcr_session_type,
            'subject_title' => $vcrSession->subject->name,
        ];
        return $transformerData;
    }

    public function includeActions($vcrSession)
    {
        $actions = [];
        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.instructor.online-sessions.listSessionParticipants', ['sessionId' => $vcrSession->id]),
            'label' => trans('vcr.session participants'),
            'method' => 'GET',
            'key' => APIActionsEnums::LIST_VCR_PARTICIPANTS
        ];

        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }
}
