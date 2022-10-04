<?php


namespace App\OurEdu\BaseNotification\Jobs;

use App\OurEdu\BaseApp\Enums\DynamicLinksEnum;
use App\OurEdu\BaseNotification\Enums\NotificationEnums;
use App\OurEdu\BaseNotification\NotifierFactory\NotifierFactory;
use App\OurEdu\BaseNotification\NotifierFactory\NotifierFactoryInterface;
use App\OurEdu\Notifications\Enums\NotificationEnum;
use App\OurEdu\Quizzes\Models\AllQuizStudent;
use App\OurEdu\Quizzes\Repository\QuizRepository\QuizRepository;
use App\OurEdu\Quizzes\Quiz;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\VCRSchedules\Models\TrackedVCRNotification;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSchedules\VCRSessionEnum;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotificationHomeWorkStudentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var User
     */
    private $users;
    /**
     * @var Quiz
     */
    private $homeWork;

    /**
     * Create a new job instance
     * @param Collection $studentUsers
     * @param homeWork $homeWork
     */
    public function __construct(Collection $studentUsers, Quiz $homeWork)
    {
        $this->users = $studentUsers;
        $this->homeWork = $homeWork;
    }

    /**
     * Execute the job.
     * @param NotifierFactoryInterface $notifierFactory
     * @return void
     * @throws \Exception
     */
    public function handle(NotifierFactoryInterface $notifierFactory)
    {
        if($this->users && $this->homeWork){
            foreach ($this->users as $studentUser) {
                if(!$studentUser->user)
                    return false;
                $url = getDynamicLink(
                    DynamicLinksEnum::STUDENT_HOMEWORK,
                    [
                        'homeworkId' => $this->homeWork->id,
                        'portal_url' => env('STUDENT_PORTAL_URL')
                    ]
                );

                $notificationData = [
                    'users' => collect([$studentUser->user]),
//                    NotificationEnums::MAIL => [
//                        'user_type' => UserEnums::STUDENT_TYPE,
//                        'data' => ['url' => $url, 'lang' => 'ar'],
//                        'subject' => trans('notification.home_work', [], 'ar'),
//                        'view' => 'homework'
//                    ],
                    NotificationEnums::FCM => [
                        'data' => [
                            'title' => buildTranslationKey('notification.home_work'),
                            'body' => $this->notificationBody($this->homeWork),
                            'data' => [
                                'screen_type' => NotificationEnum::STUDENT_HOMEWORK,
                                'session_id' => $this->homeWork->id,
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

    private function notificationBody($homeWork)
    {
        return buildTranslationKey('notification.home_work', [
            'homeWork_title' => $homeWork->quiz_title,
            'subject_name' => $homeWork->subject->name,
            'instructor_name' =>$homeWork->classroomSession?$homeWork->classroomSession->instructor->name:($homeWork->creator->type == 'school_instructor'?$homeWork->creator->first_name.' '.$homeWork->creator->last_name:'-'),
            'finish_time' => Carbon::parse($homeWork->end_at)->format('H:i')
        ]);
    }
}
