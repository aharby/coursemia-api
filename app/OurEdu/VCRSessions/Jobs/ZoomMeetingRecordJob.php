<?php


namespace App\OurEdu\VCRSessions\Jobs;

use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSessions\General\Traits\Zoom;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ZoomMeetingRecordJob implements ShouldQueue
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

    public function handle()
    {
        $meetingId = $this->vcrSession->zoom_meeting_id;

        $path = 'meetings/' . $meetingId . '/recordings';
        $response = $this->zoomGet($path);
        $meeting = json_decode($response->body(), true);

        if (isset($meeting['id'])) {
            $accessToken = $this->generateZoomToken();
            $records = $this->getZoomMeetingRecord($meeting);

            $k = 1;
            foreach ($records as $record) {
                $recordId = $record['id'];
                $downloadUrl = $record['download_url'] . '?access_token=' . $accessToken;
                $file = Http::get($downloadUrl)->getBody();
                $filename = $this->vcrSession->subject_name . '-' . $k . "." . $record['file_type'];
                $filePath = $this->vcrSession->id . '/' . $filename;
                $this->saveMeetingRecordToS3($file, $filePath, $filename, $record);
                $k ++;
            }

            $this->deleteZoomRecordings($meetingId);
        } else {
            // 5 minutes
            $this->release(5 * 60);
        }
    }

    private function getZoomMeetingRecord(array $meeting)
    {
        return array_filter(
            $meeting['recording_files'],
            function ($record, $key) {
                return $record['recording_type'] === 'shared_screen_with_speaker_view';
            },
            ARRAY_FILTER_USE_BOTH
        );
    }

    private function saveMeetingRecordToS3($file, $filePath, $filename, $record)
    {
        if (Storage::disk('s3Recording')->put($filePath, $file)) {
            $sessionMedia = $this->vcrSession->recordedFile()->create(
                [
                   'source_filename' => $filename,
                   'filename' => $filePath,
                   'url' => Storage::disk('s3Recording')->url($filePath),
                   'mime_type' => $record['file_type'],
                   'extension' => $record['file_extension'],
                   'status' => 1
                ]
            );
        } else {
            $this->saveMeetingRecordToS3($file, $filePath, $filename, $record);
        }
    }

    private function deleteZoomRecordings(int $meetingId)
    {
        $deleteZoomRecordPath = 'meetings/' . $meetingId . '/recordings';
        $this->zoomDelete($deleteZoomRecordPath, ['action' => 'delete']);
    }
}
