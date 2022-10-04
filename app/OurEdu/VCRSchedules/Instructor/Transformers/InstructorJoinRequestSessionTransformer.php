<?php


namespace App\OurEdu\VCRSchedules\Instructor\Transformers;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class InstructorJoinRequestSessionTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [

    ];
    protected array $availableIncludes = [
    ];
    private $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform($data)
    {
        $transformerData = [
            'id' => Str::uuid(),
            'sessionId' => $data['sessionId'],
            'sessionType' => $data['sessionType'],
            'sessionUrl' => $data['sessionUrl'],
            'isHost' => $data['is_host'] ?? "",
            'zoomZakToken' => $data['zoom_zak_token'] ?? "",
            'initSdkJwtToken' => $data['init_sdk_jwt_token'] ?? "",
            'userZoomId' => $data['user_zoom_id'] ?? "",
            'meetingId' => $data['meeting_id'] ?? "",
            'meetingPassword' => $data['meeting_password'] ?? "",
            'meetingType' => $data['meeting_type'] ?? "",
            'vcrSessionType' => $data['vcr_session_type'] ?? "",
        ];

        return $transformerData;
    }
}
