<?php

namespace App\OurEdu\GeneralQuizzes\Homework\Jobs;

use App\OurEdu\BaseApp\Enums\V2\DynamicLinksEnum;
use App\OurEdu\BaseNotification\Enums\NotificationEnums;
use App\OurEdu\BaseNotification\NotifierFactory\NotifierFactoryInterface;
use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NotificationHomeworkStudentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Collection
     */
    private $users;
    /**
     * @var GeneralQuiz
     */
    private $homework;
    private GeneralQuizRepositoryInterface $generalQuizRepo;
    /**
     * NotificationHomeWorkStudentsJob constructor.
     * @param Collection $users
     * @param GeneralQuiz $homework
     */

    private $linkType;

    public function __construct(GeneralQuiz $homework)
    {
        $this->generalQuizRepo = app(GeneralQuizRepositoryInterface::class);
        $this->homework = $homework;
    }

    public function handle(NotifierFactoryInterface $notifierFactory)
    {
        $this->users = $this->generalQuizRepo->getGeneralQuizStudents($this->homework, false);
        if ($this->users && $this->homework && $this->homework->is_active && $this->homework->published_at) {
            foreach ($this->users as $user) {
                $this->setLinkType();
                try {
                    $url = getDynamicLink(
                        $this->linkType,
                        [
                            'homework_id' => $this->homework->id,
                            'portal_url' => env('STUDENT_PORTAL_URL'),
                            'course_id' => $this->homework->course->id ?? null,
                        ]
                    );

                    $notificationData = [
                        "users" => collect([$user]),
                        NotificationEnums::FCM => [
                            'data' => [
                                'title' => $this->notificationBody($this->homework),
                                'body' => $this->notificationBody($this->homework),
                                'data' => [
                                    'screen_type' => 'new_'.$this->homework->quiz_type,
                                    'general_quiz_id' => (int) $this->homework->id,
                                ],
                                'url' => $url
                            ]
                        ]
                    ];

                    $notifierFactory->send($notificationData);
                } catch (\Throwable $exception) {
                    Log::error($exception->getMessage(), ['file'=>$exception->getFile()]);
                }
            }

            return true;
        }

        return false;
    }

    private function notificationBody(GeneralQuiz $homework)
    {
        if ($homework->quiz_type == GeneralQuizTypeEnum::FORMATIVE_TEST) {
            return buildTranslationKey(
                'notification.formative_test_notification',
                [
                 'subject_name' => $homework->subject?->name,
                 'title' => $homework->title,
                 'finish_time' => Carbon::parse($homework->end_at)->format('H:i')
                 ]
            );
        }
        return buildTranslationKey(
            'notification.general_quizzes_homework',
            [
            'homework_type' => trans('app.'.$homework->quiz_type),
            'homeWork_title' => $homework->title,
            'subject_name' => $homework->subject?->name,
            'instructor_name' => $homework->creator?->name,
            'finish_time' => Carbon::parse($homework->end_at)->format('H:i')
            ]
        );
    }

    private function setLinkType()
    {
        $this->linkType =  match ($this->homework->quiz_type) {
            GeneralQuizTypeEnum::PERIODIC_TEST, GeneralQuizTypeEnum::FORMATIVE_TEST =>  DynamicLinksEnum::STUDENT_PERIODIC_TEST,
            GeneralQuizTypeEnum::COURSE_HOMEWORK => DynamicLinksEnum::STUDENT_COURSE_HOMEWORK,
            GeneralQuizTypeEnum::HOMEWORK => DynamicLinksEnum::STUDENT_HOMEWORK
        };
    }
}
