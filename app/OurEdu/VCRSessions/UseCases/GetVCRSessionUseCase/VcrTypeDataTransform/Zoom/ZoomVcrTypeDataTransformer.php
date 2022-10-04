<?php


namespace App\OurEdu\VCRSessions\UseCases\GetVCRSessionUseCase\VcrTypeDataTransform\Zoom;

use App\OurEdu\Users\UseCases\CreateZoomUserUserCase\CreateZoomUserUseCaseInterface;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSessions\General\Enums\VCRProvidersEnum;
use App\OurEdu\VCRSessions\General\Traits\Zoom;
use App\OurEdu\VCRSessions\Repositories\ZoomHostRepositoryInterface;
use App\OurEdu\VCRSessions\UseCases\GetVCRSessionUseCase\VcrTypeDataTransform\VcrTypeDataTransform;
use Carbon\Carbon;
use Firebase\JWT\JWT;

class ZoomVcrTypeDataTransformer extends VcrTypeDataTransform
{
    use Zoom;

    private ZoomHostRepositoryInterface $zoomHostRepository;
    private CreateZoomUserUseCaseInterface $createZoomUserUseCase;

    /**
     * ZoomVcrTypeDataTransformer constructor.
     */
    public function __construct(VCRSession $vcrSession)
    {
        parent::__construct($vcrSession);
        $this->zoomHostRepository = app(ZoomHostRepositoryInterface::class);
        $this->createZoomUserUseCase = app(CreateZoomUserUseCaseInterface::class);
        $this->createMeeting();
    }

    public function getData(): array
    {
        $this->vcrSession->load('zoomHost');
        $this->user->load('zoom');
        $data = parent::getData();
        $append = [];
        $append['is_host'] = $this->user->type == UserEnums::SCHOOL_INSTRUCTOR || $this->user->type == UserEnums::INSTRUCTOR_TYPE;
        $append['zoom_zak_token'] = $this->getUserToken(true);
        $append['init_sdk_jwt_token'] = $this->getSdkJwtToken();
        $append['user_zoom_id'] = $this->getUserZoomId();
        $append['meeting_id'] = $this->vcrSession->zoom_meeting_id;
        $append['meeting_password'] = $this->vcrSession->zoom_meeting_password;

        return array_merge($data, $append);
    }

    private function getUserZoomId(): string
    {
        if ($this->user->type == UserEnums::SCHOOL_INSTRUCTOR || $this->user->type == UserEnums::INSTRUCTOR_TYPE) {
            return $this->vcrSession->zoomHost->zoom_user_id ?? '';
        }

        if (!$this->user->zoom) {
            $this->createZoomUserUseCase->createUser($this->user);
            $this->user->load('zoom');
        }

        return $this->user->zoom->zoom_id ?? '';
    }

    /**
     * you have to return the meeting type that you extends this class for it like agora and zoom
     *
     * @return string
     */
    protected function getMeetingType(): string
    {
        return VCRProvidersEnum::ZOOM;
    }

    private function getUserToken(bool $isZAK = false): string
    {
        if (!$this->vcrSession->zoomHost) {
            return '';
        }
        $query = [];
        $query['type'] = 'zak';
        $query['ttl'] = $this->calculateTTL();
        $pathToken = "/users/{$this->getUserZoomId()}/token";
        $token = $this->zoomGet($pathToken, $query);

        return json_decode($token->body(), true)['token'] ?? '';
    }

    private function calculateTTL()
    {
        return Carbon::now()->diffInSeconds($this->vcrSession->time_to_end);
    }

    private function getSdkJwtToken()
    {
        $key = env('ZOOM_SDK_KEY', 'F8onbyh6yASPX4dTthYTnTOxsYBNFDWIpNFA');
        $secret = env('ZOOM_SDK_SECRET', 'o2CzPwtW8nhgYUOvifRqxv27oQUB54g9nQD3');

        $payload =[
            'appKey' => $key,
            'iat' => Carbon::now()->unix(),
            'exp' => Carbon::now()->addDay()->unix(),
            'tokenExp' => Carbon::now()->addDay()->unix()
        ];

        return JWT::encode($payload, $secret, 'HS256');
    }

    public function createMeeting(): void
    {
        if (isset($this->vcrSession->zoom_meeting_id)) {
            return ;
        }

        $zoomHost = $this->zoomHostRepository->getAvailableHost($this->vcrSession);

        $path = "users/{$zoomHost->zoom_user_id}/meetings";
        $startTime = Carbon::parse($this->vcrSession->time_to_start);
        $endTime = Carbon::parse($this->vcrSession->time_to_end);
        $duration = $startTime->diffInMinutes($endTime);
        $this->vcrSession->load('subject');
        $password = 'password';

        $meeting = $this->zoomPost(
            $path,
            [
            'topic' => $this->vcrSession->subject->name ?? '_',
            'type' => 2,
            'start_time' => $this->toZoomTimeFormat($this->vcrSession->time_to_start ?? now()),
            'duration' => $duration ?? 60,
            'agenda' => $this->vcrSession->subject->name ?? '_',
            'password' => $password,
            'timezone' => 'Asia/Riyadh',
            'settings' => [
                'host_video' => false,
                'participant_video' => false,
                'waiting_room' => false,
                'mute_upon_entry' => true,
                'auto_recording' => 'cloud',
                'encryption_type' => 'enhanced_encryption',
                'join_before_host' => false,
                'meeting_authentication' => true,
                'authenticated_domains' => '*.ikcedu.net'
            ]
            ]
        );
        $meeting = json_decode($meeting->body(), true);

//        if (!isset($meeting['id']) and $zoomHost) {
//            $this->zoomHostRepository->freeUsedHost($zoomHost);
//        }

        $this->vcrSession->zoom_meeting_id = $meeting['id'];
        $this->vcrSession->zoom_meeting_password = $password;
        $this->vcrSession->zoom_host_id = $zoomHost?->id;
        $this->vcrSession->save();
    }
}
