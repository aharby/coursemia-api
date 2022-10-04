<?php

namespace App\OurEdu\Exams\UseCases\FinishExamUseCase;

use App\OurEdu\Users\UserEnums;
use Illuminate\Support\Facades\App;
use App\OurEdu\BaseApp\Enums\UrlActionEnums;
use App\OurEdu\BaseApp\Enums\DynamicLinksEnum;
use App\OurEdu\Notifications\Enums\NotificationEnum;
use App\OurEdu\BaseNotification\Enums\NotificationEnums;
use App\OurEdu\BaseNotification\NotifierFactory\NotifierFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\OurEdu\BaseNotification\NotifierFactory\NotifierFactoryInterface;

class NotifyParentsAboutExamResultUseCase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $notifierFactory;
    private $studentUser;
    private $exam;

    public function __construct($exam,$studentUser)
    {
        $this->studentUser = $studentUser;
        $this->exam = $exam;
    }

    public function handle(NotifierFactoryInterface $notifierFactory)
    {

        if (!$this->studentUser->parents->isEmpty())
        {
            $notificationData = [
                'users' => $this->studentUser->parents,
                NotificationEnums::MAIL => [
                    'user_type' => UserEnums::PARENT_TYPE,
                    'data' => ['url' => getDynamicLink(DynamicLinksEnum::studentFinishExam, [
                        'examId' => $this->exam->id,
                        'portal_url' => env('STUDENT_PORTAL_URL'),
                    ]), 'lang' => App::getLocale()],
                    // TODO:: to be changed to users lang
                    'subject' => trans('emails.Your child exam results', [], App::getLocale()),
                    'view' => 'childExamResultsMail'
                ],
                NotificationEnums::FCM => [
                    'data' => [
                        'title' => buildTranslationKey('notification.Your child exam results'),
                        'body' => buildTranslationKey('notification.Your child exam results, check it out'),
                        'data' => [
                            'exam_id'=>$this->exam->id,
                            'screen_type' => NotificationEnum::NOTIFY_PARENT_ABOUT_EXAM_RESULT,
                        ],
                        'url' => getDynamicLink(DynamicLinksEnum::studentFinishExam, [
                            'examId' => $this->exam->id,
                            'portal_url' => env('STUDENT_PORTAL_URL'),
                        ]),
                    ]
                ]
            ];

            $notifierFactory->send($notificationData);
        }
    }
}
