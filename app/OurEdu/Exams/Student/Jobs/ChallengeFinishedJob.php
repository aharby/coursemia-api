<?php

namespace App\OurEdu\Exams\Student\Jobs;

use App\OurEdu\BaseApp\Enums\DynamicLinksEnum;
use App\OurEdu\BaseApp\Enums\DynamicLinkTypeEnum;
use App\OurEdu\BaseNotification\Enums\NotificationEnums;
use App\OurEdu\BaseNotification\NotifierFactory\NotifierFactoryInterface;
use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Exams\Models\ExamChallenge;
use App\OurEdu\Notifications\Enums\NotificationEnum;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ChallengeFinishedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Exam $exam)
    {
    }

    public function handle(NotifierFactoryInterface $notifierFactory)
    {
        try {
            $url = getDynamicLink(
                DynamicLinksEnum::STUDENT_DYNAMIC_URL,
                [
                    'link_name' => 'student_view_challenged_results',
                    'firebase_url' => env('FIREBASE_URL_PREFIX'),
                    'portal_url' => env('STUDENT_PORTAL_URL'),
                    'query_param' =>'competition_id%3D'.$this->exam->id.'%26target_screen%3D'.DynamicLinkTypeEnum::VIEW_CHALLNGED_RESULTS,
                    'android_apn' => env('ANDROID_APN','com.ouredu.students')
                ]
            );

            $user = $this->exam->student->user;
            $challengedExam = ExamChallenge::query()->where('related_exam_id', $this->exam->id)->with(
                'exam.student.user'
            )->first();
            if ($challengedExam) {
                $notificationData = [
                    "users" => collect([$challengedExam->exam->student->user]),
                    NotificationEnums::FCM => [
                        'data' => [
                            'title' => buildTranslationKey(
                                'notification.student challenge finished',
                                ['Student_name' => $user->name]
                            ),
                            'body' => buildTranslationKey(
                                'notification.student challenge finished',
                                ['Student_name' => $user->name]
                            ),
                            'data' => [
                                'screen_type' => NotificationEnum::VIEW_CHALLNGED_RESULTS,
                            ],
                            'url' => $url,
                        ]
                    ]
                ];
            }
            $notifierFactory->send($notificationData);
        } catch (Throwable $exception) {
            Log::error($exception->getMessage(), ['file' => $exception->getFile()]);
        }
    }

}
