<?php


namespace App\OurEdu\BaseNotification\Jobs;

use App\OurEdu\BaseApp\Enums\DynamicLinksEnum;
use App\OurEdu\BaseNotification\Enums\NotificationEnums;
use App\OurEdu\BaseNotification\NotifierFactory\NotifierFactoryInterface;
use App\OurEdu\Users\Auth\Enum\TokenNameEnum;
use App\OurEdu\Users\Auth\TokenManager\TokenManagerInterface;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSchedules\UseCases\VCRSessionUseCase\VCRSessionUseCaseInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class InstructorSessionNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $session;

    /**
     * @var TokenManagerInterface
     */
    private $tokenManager;
    /**
     * @var VCRSessionUseCaseInterface
     */
    private VCRSessionUseCaseInterface $VCRSessionUseCase;

    /**
     * Create a new job instance.
     *
     * @param  VCRSession  $session
     * @return void
     */
    public function __construct(VCRSession $session)
    {
        $this->session = $session;
        $this->tokenManager = app(TokenManagerInterface::class);
        $this->VCRSessionUseCase = app(VCRSessionUseCaseInterface::class);
    }

    /**
     * Execute the job.
     *
     * @param  VCRSession  $session
     * @return void
     */
    public function handle(NotifierFactoryInterface $notifierFactory)
    {
        $instructor = $this->session->instructor;
        $sessionName = null;
        if ($this->session->vcr_session_type == 'live_session')
            $sessionName = $this->session->liveSession->name;
        else
            $sessionName = $this->session->course->name;

        $token = $this->tokenManager->createUserToken(TokenNameEnum::DYNAMIC_lINKS_Token, $instructor);
        $url = getDynamicLink(
            DynamicLinksEnum::INSTRUCTOR_JOIN_ROOM,
            [
                'session_id' => $this->session->id,
                'token' => $token,
                'type' => $this->session->vcr_session_type,
                'portal_url' => env('VCR_PORTAL_URL', 'https://vcr.ta3lom.com')
            ]
        );

        $data = [
            'users'                  => $instructor,
            NotificationEnums::FCM  => [
                'data' => [
                    'title' => buildTranslationKey("notification.{$this->session->vcr_session_type}"),
                    'body'  => buildTranslationKey(
                        "notification.{$this->session->vcr_session_type}_content",
                        [
                            'session_name' => $sessionName,
                            'from' => date('H:i:s', strtotime($this->session->time_to_start)),
                            'to' => date('H:i:s', strtotime($this->session->time_to_end))
                        ]
                    ),
                    'url' => $url,
                    'data'  => [
                        'screen_type' => $this->session->vcr_session_type,
                        'session_id' => $this->session->course_session_id,
                        'vcr_session_id' => $this->session->id,
                        'meeting_type' => $this->VCRSessionUseCase->getSessionMeetingProvider($this->session),
                        'vcr_session_type' => $this->session->vcr_session_type ?? "",
                    ],
                ]
            ]
        ];

        $notifierFactory->send($data);
    }
}
