<?php


namespace App\OurEdu\BaseNotification\Jobs;

use App\OurEdu\BaseApp\Enums\DynamicLinksEnum;
use App\OurEdu\BaseNotification\Enums\NotificationEnums;
use App\OurEdu\BaseNotification\NotifierFactory\NotifierFactoryInterface;
use App\OurEdu\Courses\Enums\CourseSessionEnums;
use App\OurEdu\Notifications\Enums\NotificationEnum;
use App\OurEdu\Users\Auth\Enum\TokenNameEnum;
use App\OurEdu\Users\Auth\TokenManager\TokenManagerInterface;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Users\User;
use App\OurEdu\VCRSchedules\Models\TrackedVCRNotification;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSchedules\UseCases\VCRSessionUseCase\VCRSessionUseCase;
use App\OurEdu\VCRSchedules\VCRSessionEnum;
use App\OurEdu\VCRSessions\General\Enums\VCRProvidersEnum;
use App\OurEdu\VCRSessions\General\Traits\Zoom;
use App\OurEdu\VCRSessions\Repositories\ZoomHostRepositoryInterface;
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

class NotificationStudentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Zoom;

    /**
     * @var User
     */
    private $students;
    /**
     * @var VCRSession
     */
    private $VCRSession;
    /**
     * @var TokenManagerInterface
     */
    private $tokenManager;
    /**
     * @var VCRSessionUseCase
     */
    private $VCRSessionUseCase;
    private bool $canNotifyInstructor;

    /**
     * Create a new job instance
     * @param Collection $studentUsers
     * @param VCRSession $VCRSession
     * @param bool $canNotifyInstructor
     */
    public function __construct(Collection $studentUsers, VCRSession $VCRSession, bool $canNotifyInstructor = true)
    {
        $this->students = $studentUsers;
        $this->VCRSession = $VCRSession;
        $this->tokenManager = app(TokenManagerInterface::class);
        $this->VCRSessionUseCase = app(VCRSessionUseCase::class);
        $this->canNotifyInstructor = $canNotifyInstructor;
    }

    /**
     * Execute the job.
     * @param NotifierFactoryInterface $notifierFactory
     * @return void
     * @throws Exception
     */
    public function handle(NotifierFactoryInterface $notifierFactory,ZoomHostRepositoryInterface $hostRepository)
    {
        // set the meeting type of the session
//        $this->VCRSession->meeting_type = $this->VCRSessionUseCase->getSessionMeetingProvider($this->VCRSession);
//        $this->VCRSession->save();
        if ($this->VCRSession->courseSession ) {
            if ($this->VCRSession->courseSession->status == CourseSessionEnums::CANCELED ||
                ($this->VCRSession->courseSession->course && $this->VCRSession->courseSession->course->is_active != 1)){
                return;
            }
        }
        if ($this->VCRSession->meeting_type == null){
            $this->handleTypeSession($hostRepository);
        }


        if ($this->canNotifyInstructor) {
            $this->notifyInstructor($notifierFactory);
//            event(new InstructorTimeTable($this->VCRSession->instructor->branch_id, $this->VCRSession->id, $this->VCRSession->classroom_session_id, $this->VCRSession->meeting_type));
        }

        foreach ($this->students as $student) {
            $user = $student;
            if ($student instanceof Student) {
                $user = $student->user;
            }

            $this->notifyStudent($notifierFactory, $user);
        }
//        event(new StudentTimeTable($this->VCRSession->classroom_id, $this->VCRSession->id, $this->VCRSession->classroom_session_id, $this->VCRSession->meeting_type));
    }

    private function notificationBody($vcrSession, $sessionInstructor)
    {
        $instructorName = '';
        if ($sessionInstructor instanceof User) {
            $instructorName = $sessionInstructor->name;
        }
        if ($vcrSession->vcr_session_type == VCRSessionEnum::SCHOOL_SESSION) {
            return buildTranslationKey(
                'notification.vcr_school_session',
                [
                    'subject_name' => $vcrSession->subject_name,
                    'instructor_name' => $instructorName,
                    'finish_time' => Carbon::parse($vcrSession->time_to_end)->format('H:i')
                ]
            );
        }

        if (in_array($vcrSession->vcr_session_type, [VCRSessionEnum::COURSE_SESSION_SESSION, VCRSessionEnum::LIVE_SESSION_SESSION])) {
            return buildTranslationKey(
                'notification.vcr_course_session',
                [
                    'session_content' => $vcrSession->courseSession->content,
                    'course_name' => $vcrSession->course->name,
                    'instructor_name' => $instructorName,
                    'finish_time' => Carbon::parse($vcrSession->time_to_end)->format('H:i')
                ]
            );
        }
        return buildTranslationKey('notification.vcr_session');
    }

    /**
     * @param NotifierFactoryInterface $notifierFactory
     * @param User $user
     * @param $body
     * @param array $data
     */
    private function notifyUser(NotifierFactoryInterface $notifierFactory, User $user, $body, array $data)
    {
        $token = $this->tokenManager->createUserToken(TokenNameEnum::DYNAMIC_lINKS_Token, $user);
        $meetingType = $this->getSessionMeetingType();
        $portalUrl = $meetingType == VCRProvidersEnum::AGORA ?
            env('VCR_PORTAL_URL', 'https://vcr.ta3lom.com') :
            env("QUDRAT_FRONT_APP") .
            "static/qudrat-app/" . $this->VCRSession->id . "?type=" . $this->VCRSession->vcr_session_type;
        $url = getDynamicLink(
            DynamicLinksEnum::INSTRUCTOR_JOIN_ROOM,
            [
                'session_id' => $this->VCRSession->id,
                'token' => $token,
                'type' => $this->VCRSession->vcr_session_type,
                'portal_url' => $portalUrl
            ]
        );

        $data['meeting_type'] = $meetingType;
        $data['vcr_session_id'] = $this->VCRSession->id;
        $data['vcr_session_type'] = $this->VCRSession->vcr_session_type;


        $notificationData = [
            'users' => collect([$user]),
            NotificationEnums::FCM => [
                'data' => [
                    'title' => buildTranslationKey('notification.vcr_session'),
                    'body' => $body,
                    'data' => $data,
                    'url' => $url
                ]
            ]
        ];
        $notifierFactory->send($notificationData);
        TrackedVCRNotification::query()
            ->create(
                [
                    'vcr_session_id' => $this->VCRSession->id,
                    'vcr_session_type' => $this->VCRSession->vcr_session_type,
                    'user_id' => $user->id,
                    'user_role' => $user->type
                ]
            );
    }

    /**
     * @param NotifierFactoryInterface $notifierFactory
     * @param User $student
     */
    public function notifyStudent(NotifierFactoryInterface $notifierFactory, User $student)
    {
        try {
            $body = $this->notificationBody($this->VCRSession, $this->VCRSession->instructor);
            $data = [
                'screen_type' => $this->studentScreenType(),
                'session_id' => $this->VCRSession->id,
            ];

            $this->notifyUser($notifierFactory, $student, $body, $data);
        } catch (Throwable $e) {
            Log::error($e);
        }
    }

    /**
     * @param NotifierFactoryInterface $notifierFactory
     */
    public function notifyInstructor(NotifierFactoryInterface $notifierFactory)
    {
        try {
            $instructor = $this->VCRSession->instructor;
            $body = buildTranslationKey('notification.vcr_session');
            $data = [
                'screen_type' => $this->instructorScreenType(),
            ];

            $this->notifyUser($notifierFactory, $instructor, $body, $data);
//            event(new InstructorTimeTable($instructor->branch_id,$this->VCRSession->id,$this->VCRSession->classroom_session_id, $this->VCRSession->meeting_type));
        } catch (Throwable $e) {
            Log::error($e);
        }
    }

    private function instructorScreenType()
    {
        switch ($this->VCRSession->meeting_type) {
            case VCRProvidersEnum::AGORA:
                return NotificationEnum::NOTIFY_INSTRUCTOR_VCR_AGORA_SESSION;
            case VCRProvidersEnum::ZOOM:
                return NotificationEnum::NOTIFY_INSTRUCTOR_VCR_ZOOM_SESSION;
        }
    }

    private function studentScreenType()
    {
        switch ($this->VCRSession->meeting_type) {
            case VCRProvidersEnum::AGORA:
                return NotificationEnum::NOTIFY_STUDENT_VCR_AGORA_SESSION;
            case VCRProvidersEnum::ZOOM:
                return NotificationEnum::NOTIFY_STUDENT_VCR_ZOOM_SESSION;
        }
    }

    private function handleTypeSession(ZoomHostRepositoryInterface $hostRepository)
    {
        $meetingType = $this->getSessionMeetingType();
        $this->VCRSession->meeting_type = $meetingType;
        $this->VCRSession->save();
        if ($meetingType == VCRProvidersEnum::ZOOM) {
            $getFreeHostUser = $hostRepository->getAvailableHost($this->VCRSession);
            $path = "users/{$getFreeHostUser->zoom_user_id}/meetings";
            $startTime = Carbon::parse($this->VCRSession->time_to_start);
            $endTime = Carbon::parse($this->VCRSession->time_to_end);
            $duration = $startTime->diffInMinutes($endTime);
            $this->VCRSession->load('subject');
            $password = 'password';

            $meeting = $this->zoomPost(
                $path,
                [
                    'topic' => $this->VCRSession->subject->name ?? '_',
                    'type' => 2,
                    'start_time' => $this->toZoomTimeFormat(
                        (new Carbon($this->VCRSession->time_to_start))->format('Y-m-d\TH:i:s\Z')
                    ),
                    'duration' => $duration ?? 60,
                    'agenda' => $this->VCRSession->subject->name ?? '_',
                    'password' => $password,
                    'timezone' => 'Asia/Riyadh',
                    'settings' => [
                        'host_video' => false,
                        'participant_video' => false,
                        'waiting_room' => false,
                        'mute_upon_entry' => true,
                        'auto_recording' => 'cloud',
                        'encryption_type' => 'enhanced_encryption',
                        'join_before_host' => false,
                        'meeting_authentication' => true,
                        'authenticated_domains' => '*.ikcedu.net'
                    ]
                ]
            );
            // if rate limit api exceed dispatch again
            $meeting = json_decode($meeting->body(), true);
            if (!isset($meeting['id'])) {
                Log::error('zoom wait create meeting, session id is '.$this->VCRSession->id, $meeting);
                self::dispatch(self::class)->delay(2);
                return;
            }
            $this->VCRSession->zoom_meeting_id = $meeting['id'];
            $this->VCRSession->zoom_meeting_password = $password;
            $this->VCRSession->zoom_host_id = $getFreeHostUser?->id;
        }
        $this->VCRSession->save();
    }

    private function getSessionMeetingType(): string
    {
        $this->VCRSession->load('classroom.branch.schoolAccount');

        $meetingType = $this->VCRSession->classroom?->branch?->meeting_type ?? '';
        return in_array($meetingType, VCRProvidersEnum::getList()) ?
            $meetingType :
            $this->getSystemMeetingType();
    }

    private function getSystemMeetingType(): string
    {
        $configs = getConfigs();
        $systemMeetingType = $configs['meeting_type'][''] ?? '';

        return in_array($systemMeetingType, VCRProvidersEnum::getList()) ?
            $systemMeetingType :
            VCRProvidersEnum::getDefaultProvider();
    }
}
