<?php

namespace App\Console\Commands;

use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSessions\General\Traits\Zoom;
use App\OurEdu\VCRSessions\Models\RecordedVcrSession;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class RecoverZoomRecords extends Command
{
    use Zoom;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zoom:recover-records';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recover zoom records of specific records';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $records = DB::select(
            'SELECT
        url,
        COUNT(url),
           vcr_session_id,zoom_meeting_id
    FROM
        `recorded_vcr_sessions`,vcr_sessions
    where recorded_vcr_sessions.vcr_session_id = vcr_sessions.id
    && recorded_vcr_sessions.deleted_at is null
    GROUP BY url,vcr_session_id,zoom_meeting_id
    HAVING COUNT(url) > 1'
        );

        foreach ($records as $record) {
//            dd($record,$record->vcr_session_id,$record->zoom_meeting_id);
            $getMeeting = $this->zoomGet('past_meetings/'.$record->zoom_meeting_id)->body();
            $getMeeting = json_decode($getMeeting, true);
            $getMeeting = $getMeeting['uuid'];
            $getMeeting = urlencode(urlencode($getMeeting));
            $recover = $this->zoomPut(
                'meetings/'.$getMeeting.'/recordings/status',
                [
                'action' => 'recover'
                ]
            );

            if ($recover->status() === 204) {
                sleep(5);
                $session = VCRSession::query()->where('id',$record->vcr_session_id)->first();
                $path = 'meetings/' . $record->zoom_meeting_id . '/recordings';
                $response = $this->zoomGet($path);
                $meeting = json_decode($response->body(), true);
                $this->info('body .'.$response->body());

                if (isset($meeting['id'])) {
                    $accessToken = $this->generateZoomToken();
                    $getRecords = $this->getZoomMeetingRecord($meeting);
                    $k = 1;
                    foreach ($getRecords as $getRecord) {
                        $recordId = $getRecord['id'];
                        $downloadUrl = $getRecord['download_url'] . '?access_token=' . $accessToken;
                        $file = Http::get($downloadUrl)->getBody();
                        $filename = $session->subject_name . '-' . $k . "." . $getRecord['file_type'];
                        $filePath = $session->id . '/' . $filename;
                        $this->saveMeetingRecordToS3($session, $file, $filePath, $filename, $getRecord);
                        $k++;
                    }
                    $this->deleteZoomRecordings($record->zoom_meeting_id);
                    RecordedVcrSession::query()->where('vcr_session_id', $record->vcr_session_id)->delete();
                }
            }
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

    private function saveMeetingRecordToS3($session, $file, $filePath, $filename, $record)
    {
        if (Storage::disk('s3Recording')->put($filePath, $file)) {
            $sessionMedia = $session->recordedFile()->create(
                [
                    'source_filename' => $filename,
                    'filename' => $filePath,
                    'url' => Storage::disk('s3Recording')->url($filePath),
                    'mime_type' => $record['file_type'],
                    'extension' => $record['file_extension'],
                    'status' => 1
                ]
            );
        }
    }

    private function deleteZoomRecordings(int $meetingId)
    {
        $deleteZoomRecordPath = 'meetings/' . $meetingId . '/recordings';
        $this->zoomDelete($deleteZoomRecordPath, ['action' => 'delete']);
    }
}
