<?php


namespace App\OurEdu\BaseNotification\Jobs;

use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSessions\General\Enums\VCRSessionsStatusEnum;
use App\OurEdu\VCRSessions\General\Traits\Zoom;
use App\OurEdu\VCRSessions\Jobs\ZoomMeetingRecordJob;
use App\OurEdu\VCRSessions\Repositories\ZoomHostRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FinishVCRSessionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use Zoom;

    /**
     * @var VCRSession
     */
    private $vcrSession;
    private ZoomHostRepositoryInterface $zoomHostRepository;

    /**
     * Create a new job instance
     * @param  VCRSession  $vcrSession
     */
    public function __construct(VCRSession $vcrSession)
    {
        $this->vcrSession = $vcrSession;
        $this->zoomHostRepository = app(ZoomHostRepositoryInterface::class);
    }


    public function handle()
    {
        $this->vcrSession->status = VCRSessionsStatusEnum::FINISHED;
        // if vcr is not finished
        if (is_null($this->vcrSession->ended_at)) {
            $this->vcrSession->ended_at = now();
            $this->vcrSession->finishLog()->create(['closed_from' => 'queue']);
        }
        $this->vcrSession->save();
        if (is_null($this->vcrSession->zoom_meeting_id)) {
            agoraFinishSession($this->vcrSession);
        }

        $meetingID = $this->vcrSession->zoom_meeting_id;

        if ($meetingID) {
            $path = "meetings/{$meetingID}/status";

            $this->zoomPut(
                $path,
                [
                               'action' => 'end'
                ]
            );

            $this->zoomHostRepository->freeUsedHost($this->vcrSession);
        }

        CheckStudentAbsent::dispatch($this->vcrSession);

        if ($this->vcrSession->zoom_meeting_id) {
            // download records after 10minutes
            ZoomMeetingRecordJob::dispatch($this->vcrSession)->delay(now()->addHours(2))->onQueue('low')->onConnection('redisOneByOne');
        }

        return 0;
    }
}
