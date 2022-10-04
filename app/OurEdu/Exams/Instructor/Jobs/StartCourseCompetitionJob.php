<?php

namespace App\OurEdu\Exams\Instructor\Jobs;

use App\OurEdu\BaseApp\Enums\DynamicLinksEnum;
use App\OurEdu\BaseApp\Enums\DynamicLinkTypeEnum;
use App\OurEdu\BaseNotification\Enums\NotificationEnums;
use App\OurEdu\BaseNotification\NotifierFactory\NotifierFactoryInterface;
use App\OurEdu\Exams\Models\Exam;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class StartCourseCompetitionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Exam $exam, public $users)
    {
    }

    public function handle(NotifierFactoryInterface $notifierFactory)
    {
        foreach ($this->users as $user) {
            try {
                $url = getDynamicLink(
                    DynamicLinksEnum::STUDENT_DYNAMIC_URL,
                    [
                        'link_name' => 'studentJoinCourseCompetition',
                        'firebase_url' => env('FIREBASE_URL_PREFIX'),
                        'portal_url' => env('STUDENT_PORTAL_URL'),
                        'query_param' => 'competition_id%3D' . $this->exam->id . '%26target_screen%3D' . DynamicLinkTypeEnum::JOIN_COURSE_COMPETITION
                    ]
                );

                $notificationData = [
                    "users" => collect([$user]),
                    NotificationEnums::FCM => [
                        'data' => [
                            'title' => $this->notificationBody(),
                            'body' => $this->notificationBody(),
                            'data' => [
                                'screen_type' => 'new_' . $this->exam->type,
                                'exam_id' => (int)$this->exam->id,
                            ],
                            'url' => $url
                        ]
                    ]
                ];

                $notifierFactory->send($notificationData);
            } catch (Throwable $exception) {
                Log::error($exception->getMessage(), ['file' => $exception->getFile()]);
            }
        }
    }

    private function notificationBody()
    {
        return buildTranslationKey(
            'notification.course_competition_start_student',
            [
                'instructor_name' => $this->exam->creator->name,
                'start_at' => $this->exam->start_time,
                'end_time' => $this->exam->finished_time,
                'course_name' => $this->exam->course->name
            ]
        );
    }
}
