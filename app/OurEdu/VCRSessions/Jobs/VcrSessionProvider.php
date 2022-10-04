<?php

namespace App\OurEdu\VCRSessions\Jobs;

use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSessions\General\Enums\VCRProvidersEnum;
use App\OurEdu\VCRSessions\General\Traits\Zoom;
use App\OurEdu\VCRSessions\Repositories\ZoomHostRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class VcrSessionProvider implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use Zoom;

    /**
     * @var VCRSession
     */
    private VCRSession $vcrSession;

    /**
     * ZoomMeetingRecordJob constructor.
     * @param  VCRSession  $vcrSession
     */
    public function __construct(VCRSession $vcrSession)
    {
        $this->vcrSession = $vcrSession;
    }

    public function handle(ZoomHostRepositoryInterface $hostRepository)
    {
        if ($this->vcrSession->meeting_type == null){
            $meetingType = $this->getSchoolMeetingType();
            $this->vcrSession->meeting_type = $meetingType;
            Log::error('before zoom',[$meetingType]);
            if ($meetingType == VCRProvidersEnum::ZOOM) {
                Log::error('zoom',[$meetingType]);
                $getFreeHostUser = $hostRepository->getAvailableHost($this->vcrSession);
                Log::error('zoom host',[$getFreeHostUser]);
                $path = "users/{$getFreeHostUser->zoom_user_id}/meetings";
                $startTime = Carbon::parse($this->vcrSession->time_to_start);
                $endTime = Carbon::parse($this->vcrSession->time_to_end);
                $duration = $startTime->diffInMinutes($endTime);
                $this->vcrSession->load('subject');
                $password = 'password';

                $meeting = $this->zoomPost(
                    $path,
                    [
                        'topic' => $this->vcrSession->subject->name ?? '_',
                        'type' => 2,
                        'start_time' => $this->toZoomTimeFormat(
                            (new Carbon($this->vcrSession->time_to_start))->format('Y-m-d\TH:i:s\Z')
                        ),
                        'duration' => $duration ?? 60,
                        'agenda' => $this->vcrSession->subject->name ?? '_',
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
                        ]
                    ]
                );
                $meeting = json_decode($meeting->body(), true);
                Log::error('zoom Meeting',$meeting);
                $this->vcrSession->zoom_meeting_id = $meeting['id'];
                $this->vcrSession->zoom_meeting_password = $password;
                $this->vcrSession->zoom_host_id = $getFreeHostUser?->id;
            }
            $this->vcrSession->save();
        }
    }

    private function getSchoolMeetingType(): string
    {
        $this->vcrSession->load('classroom.branch.schoolAccount');

        $meetingType = $this->vcrSession->classroom->branch->meeting_type ?? '';

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
