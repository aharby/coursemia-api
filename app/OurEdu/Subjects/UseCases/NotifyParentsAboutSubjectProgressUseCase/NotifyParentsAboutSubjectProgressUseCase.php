<?php

namespace App\OurEdu\Subjects\UseCases\NotifyParentsAboutSubjectProgressUseCase;

use App\OurEdu\Users\UserEnums;
use Illuminate\Support\Facades\App;
use App\OurEdu\BaseApp\Enums\UrlActionEnums;
use App\OurEdu\Subjects\Enums\SubjectProgressEnum;
use App\OurEdu\Notifications\Enums\NotificationEnum;
use App\OurEdu\Notifications\Models\TrackedSubjectNotification;
use App\OurEdu\BaseNotification\NotifierFactory\NotifierFactoryInterface;

class NotifyParentsAboutSubjectProgressUseCase implements NotifyParentsAboutSubjectProgressUseCaseInterface
{
    private $notifierFactory;
    private $subjectProgressPercentage;
    private $studentUser;
    private $subject;

    public function __construct(NotifierFactoryInterface $notifierFactory)
    {
        $this->notifierFactory = $notifierFactory;
    }
    public function notifyParents($subject, $student)
    {
        $this->setNeededData($subject, $student);

        $notificationBy100 = TrackedSubjectNotification::where('sender_id', $this->studentUser->id)
                            ->where('notification_type', NotificationEnum::NOTIFY_PARENT_SUBJECT_PROGRESS_100)
                            ->first();

        $notificationBy50 = TrackedSubjectNotification::where('sender_id', $this->studentUser->id)
                            ->where('notification_type', NotificationEnum::NOTIFY_PARENT_SUBJECT_PROGRESS_50)
                            ->first();

        if (is_null($notificationBy100)){
            // if percentage is 100% notify by another notification
            if($this->subjectProgressPercentage == SubjectProgressEnum::PROGRESS_PERCENTAGE_100){
                $this->notifyParentsAboutSubjectProgress();
            }
        }

        if (is_null($notificationBy50)) {
            // if percentage is 50% notify by a notification
            if($this->subjectProgressPercentage >= SubjectProgressEnum::PROGRESS_PERCENTAGE_50 &&
                $this->subjectProgressPercentage < SubjectProgressEnum::PROGRESS_PERCENTAGE_100){
                $this->notifyParentsAboutSubjectProgress();
            }
        }
    }

    private function notifyParentsAboutSubjectProgress()
    {
        $notification_type = $this->subjectProgressPercentage == 100 ? NotificationEnum::NOTIFY_PARENT_SUBJECT_PROGRESS_100 : NotificationEnum::NOTIFY_PARENT_SUBJECT_PROGRESS_50;

        if (!$this->studentUser->parents->isEmpty()){
            $notificationData = [
                'users' => $this->studentUser->parents,
                'mail' => [
                    'user_type' => UserEnums::PARENT_TYPE,
                    'data'=> ['url' => UrlActionEnums::getSubjectProgressForParentUrl($this->studentUser->student->id, $this->subject->id),
                            'progressPercentage' => $this->subjectProgressPercentage,
                            'lang' => App::getLocale()],
                    'subject' => trans('emails.Your child subject progress', [], App::getLocale()),
                    'view' => 'childSubjectProgressMail'
                ],
                'fcm' => [
                    'data' => [
                        'title' => buildTranslationKey('notification.Your child subject progress'),
                        'body' => buildTranslationKey('notification.Your child subject progress, check it out'),
                        'data' => [
                            'student_id' => $this->studentUser->student->id ,
                            'subject_id' => $this->subject->id,
                            'screen_type' => $notification_type,
                        ],
                        'url' => UrlActionEnums::getSubjectProgressForParentUrl($this->studentUser->student->id, $this->subject->id),
                    ]
                ]
            ];

            $this->notifierFactory->send($notificationData);

            $studentParents = $this->studentUser->parents;
            foreach ($studentParents as $parent) {
                // create a notification tracked record
                TrackedSubjectNotification::create([
                    'sender_id' => $this->studentUser->id,
                    'sender_user_type' => $this->studentUser->type,
                    'receiver_id' => $parent->id,
                    'receiver_user_type' => $parent->type,
                    'notification_type' => $notification_type
                ]);
            }
        }
    }

    private function setNeededData($subject, $student)
    {
        $this->studentUser = $student->user;
        $this->subject = $subject;
        $progressPoints = $student->subscribe()->where('subject_id', $subject->id)->first()->subject_progress;
        $this->subjectProgressPercentage = round(($progressPoints / $subject->total_points) * 100);
    }

}
