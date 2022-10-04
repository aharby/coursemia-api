<?php


namespace App\OurEdu\BaseNotification\Jobs;

use App\OurEdu\BaseNotification\Enums\NotificationEnums;
use App\OurEdu\BaseNotification\NotifierFactory\NotifierFactoryInterface;
use App\OurEdu\Notifications\Enums\NotificationEnum;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSchedules\Models\VCRSessionPresence;
use App\OurEdu\VCRSchedules\Repository\VCRSessionParticipantsRepositoryInterface;
use App\OurEdu\VCRSchedules\Repository\VCRSessionRepositoryInterface;
use App\OurEdu\VCRSchedules\VCRSessionEnum;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckStudentAbsent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var VCRSession
     */
    private $vcrSession;

    /**
     * Create a new job instance
     * @param VCRSession $vcrSession
     */
    public function __construct(VCRSession $vcrSession)
    {
        $this->vcrSession = $vcrSession;
    }

    /**
     * Execute the job.
     * @param NotifierFactoryInterface $notifierFactory
     * @param VCRSessionParticipantsRepositoryInterface $vcrSessionParticiRepo
     * @return void
     */
    public function handle(
        NotifierFactoryInterface $notifierFactory,
        VCRSessionRepositoryInterface $VCRSessionRepo,
        VCRSessionParticipantsRepositoryInterface $vcrSessionParticiRepo
    )
    {
        //Checking if instructor attends this session
        $instructorAttends = VCRSessionPresence::where('vcr_session_id', $this->vcrSession->id)
            ->where('user_role', UserEnums::SCHOOL_INSTRUCTOR)->exists();
        if (!$instructorAttends)
            return;

        if ($this->vcrSession->vcr_session_type == VCRSessionEnum::SCHOOL_SESSION) {
            if ($this->vcrSession->classroom()->exists()){
                if ($this->vcrSession->classroom->branch()->exists()){
                        $attendedStudentsIds = VCRSessionPresence::where('vcr_session_id', $this->vcrSession->id)
                            ->where('user_role', UserEnums::STUDENT_TYPE)
                            ->pluck('user_id')->toArray();
                        $toBeNotifiedStudents = $vcrSessionParticiRepo->getAbsentStudent($this->vcrSession->classroom_id, $attendedStudentsIds);
                        $this->notifyStudentsParents($toBeNotifiedStudents, $this->vcrSession, $notifierFactory);
                }
            }

        }

    }

    private function notifyStudentsParents($absentStudentsUsers, $vcrSession, $notifierFactory)
    {
        foreach ($absentStudentsUsers as $studentUser) {
            if (!$studentUser->parents->isEmpty()) {
                $parents = $studentUser->parents;
                $notificationData = [
                    'users' => $parents,
//                    NotificationEnums::MAIL => [
//                        'user_type' => UserEnums::PARENT_TYPE,
//                        'data' => ['url' => '', 'lang' => 'ar'],
//                        'subject' => trans('notification.student_did_not_attend', [
//                            'student' => $studentUser->first_name,
//                            'parent' => $parents->first()->first_name,
//                            'subject' => $vcrSession->subject->name
//                        ], 'ar'),
//                        'view' => 'vcrSessionAbsence'
//                    ],
                    NotificationEnums::FCM => [
                        'data' => [
                            'title' => trans('notification.student_did_not_attend', [
                                'student' => trim($studentUser->first_name),
                                'parent' => trim($parents->first()->first_name),
                                'subject' => trim($vcrSession->subject->name)
                            ], 'ar'),
                            'body' => trans('notification.student_did_not_attend', [
                                'student' => trim($studentUser->first_name),
                                'parent' => trim($parents->first()->first_name),
                                'subject' => trim($vcrSession->subject->name)
                            ], 'ar'),
                            'data' => [
                                'screen_type' => NotificationEnum::NOTIFY_PARENT_ABOUT_STUDENT_ABSENCE,
                            ],
                        ]
                      ]
                    ];


                if ($this->vcrSession->classroom->branch->sms == 1){
                       $notificationData[ NotificationEnums::SMS] = [
                        'message' => trans('notification.student_did_not_attend', [
                            'student' => trim($studentUser->first_name),
                            'parent' => trim($parents->first()->first_name),
                            'subject' => trim($vcrSession->subject->name)
                        ], 'ar')
                    ];
                }
                $notifierFactory->send($notificationData);
            }
        }
    }
}
