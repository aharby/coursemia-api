<?php


namespace App\OurEdu\BaseNotification\Jobs;

use App\OurEdu\BaseNotification\Enums\NotificationEnums;
use App\OurEdu\BaseNotification\NotifierFactory\NotifierFactoryInterface;
use App\OurEdu\Notifications\Enums\NotificationEnum;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class LeaveSessionNotifyParentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var User
     */
    private $users;
    /**
     * @var VCRSession
     */
    private $VCRSession;
    /**
     * @var NotifierFactoryInterface
     */
    private $notifierFactory;
    /**
     * @var User
     */
    private $user;

    private $left_at;
    /**
     * Create a new job instance
     * @param User $user
     * @param VCRSession $VCRSession
     */
    public function __construct(User $user, VCRSession $VCRSession,$left_at)
    {
        $this->user = $user;
        $this->VCRSession = $VCRSession;
        $this->left_at = $left_at;
    }

      /**
     * Execute the job.
     * @param NotifierFactoryInterface $notifierFactory
     * @return void
     * @throws \Exception
     */
    public function handle(NotifierFactoryInterface $notifierFactory)
    {
        if (!$this->user->parents->isEmpty()) {
            $parents = $this->user->parents;
            $notificationData = [
                'users' => $parents,
                NotificationEnums::FCM => [
                    'data' => [
                        'title' => buildTranslationKey('notification.leave_session',[
                            'student_name'=>$this->user->first_name
                        ]),
                        'body' => $this->notificationBody($this->user,$this->VCRSession,$this->left_at),
                        'data' => [
                            'screen_type' => NotificationEnum::NOTIFY_PARENT_ABOUT_STUDENT_LEAVE_SESSION,
                            'vcr_session_id' => $this->VCRSession->id,
                        ],
                    ]
                ]
            ];
            $notifierFactory->send($notificationData);
        }
    }

    private function notificationBody($user, $vcrSession,$left_at)
    {
        return buildTranslationKey(
            'notification.leave_session_body',
            [
                'student_name'=>$user->first_name,
                'leave_time'=>$left_at,
                'subject_name' => $vcrSession->subject_name,
            ]
        );
        
    }
}