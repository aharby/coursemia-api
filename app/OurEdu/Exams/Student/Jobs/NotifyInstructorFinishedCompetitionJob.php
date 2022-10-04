<?php

namespace App\OurEdu\Exams\Student\Jobs;

use App\OurEdu\BaseApp\Enums\DynamicLinksEnum;
use App\OurEdu\BaseApp\Enums\DynamicLinkTypeEnum;
use App\OurEdu\BaseNotification\Enums\NotificationEnums;
use App\OurEdu\BaseNotification\NotifierFactory\NotifierFactoryInterface;
use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Notifications\Enums\NotificationEnum;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NotifyInstructorFinishedCompetitionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Exam $exam)
    {
    }

    public function handle(NotifierFactoryInterface $notifierFactory)
    {
        try {
            $url = getDynamicLink(
                DynamicLinksEnum::INSTRUCTOR_COURSE_COMPETITION_FEEDBACK,
                [
                    'portal_url' => env('STUDENT_PORTAL_URL'),
                    'exam_id' => $this->exam->id
                ]
            );

            $notificationData = [
                "users" => collect([$this->exam->creator]),
                NotificationEnums::FCM => [
                    'data' => [
                        'title' => buildTranslationKey('notification.competition feedback',['title'=> $this->exam->title]),
                        'body' => buildTranslationKey('notification.competition feedback',['title'=> $this->exam->title]),
                        'data' => [
                            'screen_type' => NotificationEnum::COMPETITION_FEEDBACK_INSTRUCTOR,
                        ],
                        'url' => $url,
                    ]
                ]
            ];

            $notifierFactory->send($notificationData);
        } catch (Throwable $exception) {
            Log::error($exception->getMessage(), ['file' => $exception->getFile()]);
        }
    }

}
