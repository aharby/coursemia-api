<?php


namespace App\OurEdu\BaseNotification\Jobs;

use App\OurEdu\BaseApp\Enums\DynamicLinksEnum;
use App\OurEdu\BaseNotification\Enums\NotificationEnums;
use App\OurEdu\BaseNotification\NotifierFactory\NotifierFactoryInterface;
use App\OurEdu\Notifications\Enums\NotificationEnum;
use App\OurEdu\Users\Auth\Enum\TokenNameEnum;
use App\OurEdu\Users\Auth\TokenManager\TokenManagerInterface;
use App\OurEdu\Users\User;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSchedules\UseCases\VCRSessionUseCase\VCRSessionUseCaseInterface;
use App\OurEdu\VCRSessions\General\Enums\VCRProvidersEnum;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class NotifyStudentVCRSessionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var User
     */
    private $user;
    /**
     * @var VCRSession
     */
    private $VCRSession;
    /**
     * @var TokenManagerInterface
     */
    private $tokenManager;
    /**
     * @var VCRSessionUseCaseInterface
     */
    private VCRSessionUseCaseInterface $VCRSessionUseCase;

    /**
     * Create a new job instance
     * @param Collection $user
     * @param VCRSession $VCRSession
     */
    public function __construct(VCRSession $VCRSession,User $user)
    {
        $this->user = $user;
        $this->VCRSession = $VCRSession;
        $this->tokenManager = app(TokenManagerInterface::class);
        $this->VCRSessionUseCase = app(VCRSessionUseCaseInterface::class);
    }

    /**
     * Execute the job.
     * @param NotifierFactoryInterface $notifierFactory
     * @return void
     * @throws Exception
     */
    public function handle(NotifierFactoryInterface $notifierFactory)
    {
        try {
            $token = $this->tokenManager->createUserToken(TokenNameEnum::DYNAMIC_lINKS_Token, $this->user);
            $meetingType = $this->VCRSessionUseCase->getSessionMeetingProvider($this->VCRSession);
            $portalUrl = $meetingType == VCRProvidersEnum::AGORA ?
                env('VCR_PORTAL_URL', 'https://vcr.ta3lom.com') :
                env("QUDRAT_FRONT_APP") .
                "static/qudrat-app/" . $this->VCRSession->id . "?type=" . $this->VCRSession->vcr_session_type;
            $url = getDynamicLink(
                DynamicLinksEnum::INSTRUCTOR_JOIN_ROOM,
                ['session_id' => $this->VCRSession->id,
                    'token' => $token,
                    'type' => $this->VCRSession->vcr_session_type,
                    'portal_url' => $portalUrl
                ]
            );

            $notificationData = [
                'users' => collect([$this->user]),
                NotificationEnums::FCM => [
                    'data' => [
                        'title' => buildTranslationKey('notification.vcr_session'),
                        'body' => $this->notificationBody($this->VCRSession, $this->VCRSession->instructor),
                        'data' => [
                            'screen_type' => NotificationEnum::NOTIFY_STUDENT_VCR_SESSION,
                            'session_id' => $this->VCRSession->id,
                            'vcr_session_id' => $this->VCRSession->id, // to be like the instructor notifications key name
                            'meeting_type' => $meetingType,
                            'vcr_session_type' => $this->VCRSession->vcr_session_type ?? "",
                        ],
                        'url' => $url
                    ]
                ]
            ];
            $notifierFactory->send($notificationData);
        } catch (Throwable $e) {
            Log::error($e);
        }

    }

    private function notificationBody($vcrSession, $sessionInstructor)
    {
        $instructorName = '';
        if ($sessionInstructor instanceof User) {
            $instructorName = $sessionInstructor->name;
        }

        return buildTranslationKey(
            'notification.requested_vcr_session',
            [
                'instructor_name' => $instructorName,
                'finish_time' => Carbon::parse($vcrSession->time_to_end)->format('H:i')
            ]
        );
    }
}
