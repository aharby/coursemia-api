<?php

namespace App\OurEdu\VCRSchedules\Instructor\Events;

use App\OurEdu\BaseApp\Api\Traits\ApiResponser;
use App\OurEdu\BaseApp\Enums\DynamicLinksEnum;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Users\Auth\Enum\TokenNameEnum;
use App\OurEdu\Users\Auth\TokenManager\TokenManagerInterface;
use App\OurEdu\VCRSessions\General\Enums\VCRProvidersEnum;
use App\OurEdu\VCRSessions\UseCases\GetVCRSessionUseCase\VcrTypeDataTransform\Zoom\ZoomVcrTypeDataTransformer;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

use App\OurEdu\VCRSchedules\Instructor\Transformers\InstructorJoinRequestSessionTransformer;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSessions\General\Enums\VCRSessionsTypeEnum;

class InstructorJoinRequestedSession implements  ShouldBroadcast
{
    use SerializesModels, Dispatchable , ApiResponser;


    private $vcrSession;
    /**
     * Create a new event instance.
     *
     * @param VCRSession $vcrSession
     */
    /**
     * @var TokenManagerInterface
     */
    private $tokenManager;
    public function __construct(VCRSession $vcrSession)
    {
        $this->vcrSession = $vcrSession;
        $this->tokenManager = app(TokenManagerInterface::class);
    }

    public function broadcastOn(){

        return new PresenceChannel('instructor-join-session.'. $this->vcrSession->id);
    }

    public function broadcastAs() {

        return 'InstructorJoinRequestedSession';
    }

    public function broadcastWith()
    {
        $data = [];
        $token = $this->tokenManager->createUserToken(
            TokenNameEnum::DYNAMIC_lINKS_Token,
            $this->vcrSession->student->user
        );
        $meetingType = $this->getSystemMeetingType();
        $portalUrl = $meetingType == VCRProvidersEnum::AGORA ?
            env('VCR_PORTAL_URL', 'https://vcr.ta3lom.com') :
            env("QUDRAT_FRONT_APP") . "static/qudrat-app/" . $this->vcrSession->id . "?type=requested_live_session";
        $url = getDynamicLink(
            DynamicLinksEnum::INSTRUCTOR_JOIN_ROOM,
            [
                'session_id' => $this->vcrSession->id,
                'token' => $token,
                'type' => VCRSessionsTypeEnum::REQUESTED_LIVE_SESSION,
                'portal_url' => $portalUrl
            ]
        );

        $data[] = [
            'sessionId' => $this->vcrSession->id,
            'sessionType' => $this->vcrSession->vcr_session_type,
            'sessionUrl' => $url,
            'meeting_type' => $meetingType,
            'vcr_session_type' => $this->vcrSession->vcr_session_type ?? "",
        ];

        return $this->transformDataMod($data, new InstructorJoinRequestSessionTransformer(), ResourceTypesEnums::INSTRUCTOR_JOIN_REQUESTED_SESSION);
    }

    private function getSystemMeetingType(): string
    {
        $configs = getConfigs();
        $systemMeetingType = $configs['meeting_type'][''] ?? '';

        return in_array($systemMeetingType, VCRProvidersEnum::getList()) ?
            $systemMeetingType :
            VCRProvidersEnum::getDefaultProvider();
    }
}
