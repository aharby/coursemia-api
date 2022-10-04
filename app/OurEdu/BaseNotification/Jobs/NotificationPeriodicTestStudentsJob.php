<?php


namespace App\OurEdu\BaseNotification\Jobs;


use App\OurEdu\BaseApp\Enums\DynamicLinksEnum;
use App\OurEdu\BaseNotification\Enums\NotificationEnums;
use App\OurEdu\BaseNotification\NotifierFactory\NotifierFactoryInterface;
use App\OurEdu\Notifications\Enums\NotificationEnum;
use App\OurEdu\Quizzes\Quiz;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotificationPeriodicTestStudentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var User
     */
    private $users;
    /**
     * @var Quiz
     */
    private $periodicTest;

    /**
     * Create a new job instance
     * @param Collection $studentUsers
     * @param periodiTest $periodicTest
     */
    public function __construct(Collection $studentUsers, Quiz $periodicTest)
    {
        $this->users = $studentUsers;
        $this->periodicTest = $periodicTest;
    }

    /**
     * Execute the job.
     * @param NotifierFactoryInterface $notifierFactory
     * @return void
     * @throws \Exception
     */
    public function handle(NotifierFactoryInterface $notifierFactory)
    {
        if($this->users && $this->periodicTest){
            foreach ($this->users as $studentUser) {
                if(!$studentUser->user)
                    return false;
                $url = getDynamicLink(
                    DynamicLinksEnum::STUDENT_PERIODIC_TEST,
                    [
                        'periodicTestId' => $this->periodicTest->id,
                        'portal_url' => env('STUDENT_PORTAL_URL')
                    ]
                );
                $notificationData = [
                    'users' => collect([$studentUser->user]),
                //    NotificationEnums::MAIL => [
                //        'user_type' => UserEnums::STUDENT_TYPE,
                //        'data' => ['url' => $url, 'lang' => 'ar'],
                //        'subject' => trans('notification.periodic_test', [], 'ar'),
                //        'view' => 'periodictest'
                //    ],
                    NotificationEnums::FCM => [
                        'data' => [
                            'title' => buildTranslationKey('notification.periodic_test'),
                            'body' => $this->notificationBody($this->periodicTest),
                            'data' => [
                                'screen_type' => NotificationEnum::STUDENT_PERIODIC_TEST,
                                'session_id' => $this->periodicTest->id,
                            ],
                            'url' => $url
                        ]
                    ]
                ];
                $notifierFactory->send($notificationData);
            }
            return true;
        }else{
            return false;
        }

    }

    private function notificationBody($periodicTest)
    {
        return buildTranslationKey('notification.periodic_test', [
            'periodic_test_title' => $periodicTest->quiz_title,
            'subject_name' => $periodicTest->subject->name,
            'instructor_name' => $periodicTest->creator && $periodicTest->creator->type == 'school_instructor'?$periodicTest->creator->first_name.' '.$periodicTest->creator->last_name:'-',
            'finish_time' => Carbon::parse($periodicTest->end_at)->format('H:i')
        ]);
    }

}
