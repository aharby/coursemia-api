<?php


namespace App\OurEdu\VCRSchedules\UseCases\VCRNotificationUseCase;


use App\OurEdu\BaseApp\Enums\DynamicLinksEnum;
use App\OurEdu\BaseNotification\NotifierFactory\NotifierFactoryInterface;
use App\OurEdu\Notifications\Enums\NotificationEnum;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\VCRSchedules\Repository\VCRSessionParticipantsRepositoryInterface;
use App\OurEdu\VCRSchedules\Repository\VCRSessionRepositoryInterface;

class VCRNotificationUseCase implements VCRNotificationUseCaseInterface
{
    private $notifierFactory;
    private $VCRSessionRepository;
    private $VCRParticipantsRepo;

    public function __construct(
        NotifierFactoryInterface $notifierFactory,
        VCRSessionRepositoryInterface $VCRSessionRepository,
        VCRSessionParticipantsRepositoryInterface $VCRSessionParticipantsRepository
    )
    {
        $this->notifierFactory = $notifierFactory;
        $this->VCRSessionRepository = $VCRSessionRepository;
        $this->VCRParticipantsRepo = $VCRSessionParticipantsRepository;
    }


    public function examGeneratedNotification($sessionId, $examId)
    {
        $sessionParticipants = $this->VCRParticipantsRepo->getSessionStudentParticipants($sessionId);
        $url =  getDynamicLink(DynamicLinksEnum::STUDENT_START_VCR_EXAM, [
            'exam_id'=> $examId,
            'portal_url' => env('STUDENT_PORTAL_URL')
        ]);

       $apiUrl= buildScopeRoute('api.student.exams.get.take', ['examId' => $examId]);
        $notificationData = [
            'users' => $sessionParticipants,
            'mail' => [
                'user_type' => UserEnums::STUDENT_TYPE,
                'data' => ['url' => $url, 'lang' => 'ar'],
                // TODO:: to be changed to users lang
                'subject' => trans('notification.new vcr exam created',[], 'ar'),
                'view' => 'vcrExam'
            ],
            'fcm' => [
                'data' => [
                    'body' =>  'notification.new vcr exam created',
                    'data'=>[
                        'screen_type' => NotificationEnum::STUDENT_VCR_EXAM,
                        'session_id'=> $sessionId,
                        'api_url'=>$apiUrl
                    ],
                    'url' => $url,
                ]
            ]
        ];
        $this->notifierFactory->send($notificationData);
    }

    public function examFinishedNotification($exam)
    {
        $sessionInstructor = $this->VCRSessionRepository->getSessionInstructor($exam->vcr_session_id);
        $url =  getDynamicLink(DynamicLinksEnum::INSTRUCTOR_VIEW_STUDENT_VCR_EXAM_FEEDBACK, [
            'session_id'=> $exam->vcr_session_id,
            'exam_id' => $exam->id,
            'portal_url' => env('STUDENT_PORTAL_URL')
        ]);
        $studentUser = $exam->student->user;

        $body =  trans('notification.student finished exam' ,['student'=> $studentUser->name, 'finishTime'=> $exam->finished_time], $sessionInstructor->language);
        $notificationData = [
            'users' => collect([$sessionInstructor]),
            'mail' => [
                'user_type' => UserEnums::INSTRUCTOR_TYPE,
                'data' => ['url' => $url, 'body' => $body, 'lang' => $sessionInstructor->language],
                'subject' => trans('notification.new exam finished' ,[], $sessionInstructor->language),
                'view' => 'vcrExamFeedback'
            ],
            'fcm' => [
                'data' => [
                    'title' => buildTranslationKey('notification.new exam finished'),
                    'body' => buildTranslationKey('notification.new exam finished'),
                    'data' => [
                        'session_id'=> $exam->vcr_session_id,
                        'exam_id'=> $exam->id,
                        'screen_type' => NotificationEnum::INSTRUCTOR_VIEW_STUDENT_VCR_EXAM_FEEDBACK,
                    ],
                    'url' => $url,
                ]
            ]
        ];
        $this->notifierFactory->send($notificationData);
    }
}
