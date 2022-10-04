<?php

namespace App\OurEdu\VCRSessions\Student\Transformers;

use League\Fractal\TransformerAbstract;

class JoinedSessionDataTransformer extends TransformerAbstract
{
    protected $params;

    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [
    ];

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform($onlineSession)
    {
        $transformerData = [
            'id' => $onlineSession->id,
            'session_token' => $onlineSession->session_token,
            'session_title' => $onlineSession->content,
            'current_participant_token' => $this->params->user_token,
            'current_participant_role' => $this->params->user_role
        ];

        return $transformerData;
    }
}
