<?php


namespace App\OurEdu\BaseNotification\Jobs;

use App\OurEdu\BaseApp\Enums\DynamicLinksEnum;
use App\OurEdu\BaseNotification\Enums\NotificationEnums;
use App\OurEdu\BaseNotification\NotifierFactory\NotifierFactoryInterface;
use App\OurEdu\Notifications\Enums\NotificationEnum;
use App\OurEdu\Users\Auth\Enum\TokenNameEnum;
use App\OurEdu\Users\Auth\TokenManager\TokenManagerInterface;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\Quizzes\Quiz;
use App\OurEdu\VCRSchedules\Models\TrackedVCRNotification;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSchedules\VCRSessionEnum;
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

class NotificationQuizJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var User
     */
    private $users;
    /**
     * @var Quiz
     */
    private $quiz;
    /**
     * @var TokenManagerInterface
     */
    private $tokenManager;

    /**
     * Create a new job instance
     * @param Collection $studentUsers
     * @param Quiz $quiz
     */
    public function __construct(Collection $studentUsers, Quiz $quiz)
    {
        $this->users = $studentUsers;
        $this->quiz = $quiz;
        $this->tokenManager = app(TokenManagerInterface::class);
    }

    /**
     * Execute the job.
     * @param NotifierFactoryInterface $notifierFactory
     * @return void
     * @throws Exception
     */
    public function handle(NotifierFactoryInterface $notifierFactory)
    {
        foreach ($this->users as $studentUser) {
            try {
                $token = $this->tokenManager->createUserToken(TokenNameEnum::DYNAMIC_lINKS_Token, $studentUser);
                $url = getDynamicLink(
                    DynamicLinksEnum::STUDENT_GET_QUIZ,
                    [
                        'quiz_id' => $this->quiz->id,
                        'token' => $token,
                        'quiz_time' => $this->quiz->quiz_time,
                        'portal_url' => env('STUDENT_PORTAL_URL')
                    ]
                );
                $notificationData = [
                    'users' => collect([$studentUser]),
                    NotificationEnums::FCM => [
                        'data' => [
                            'title' => buildTranslationKey('notification.quiz'),
                            'body' => $this->notificationBody($this->quiz, $this->quiz->classroomSession),
                            'data' => [
                                'screen_type' => NotificationEnum::SESSION_QUIZ,
                                'quiz_id' => $this->quiz->id,
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
    }

    private function notificationBody($quiz, $classroomClassSession)
    {
        return buildTranslationKey(
            'notification.quiz',
            [
                'quiz_title'=>$quiz->quiz_title,
                'quiz_time'=>$quiz->quiz_time,
                'subject_name' => $classroomClassSession->vcrSession->subject_name,
            ]
        );

    }
}
