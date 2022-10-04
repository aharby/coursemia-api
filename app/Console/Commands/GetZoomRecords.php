<?php

namespace App\Console\Commands;

use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSchedules\VCRSessionEnum;
use App\OurEdu\VCRSessions\General\Enums\VCRSessionsStatusEnum;
use App\OurEdu\VCRSessions\General\Traits\Zoom;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class GetZoomRecords extends Command
{
    use Zoom;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zoom:records';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Zoom Records';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $sessions = VCRSession::with('recordedFile')
            ->where('time_to_end','<=',now()->subHours(6))
            ->where('status',VCRSessionsStatusEnum::FINISHED)
            ->whereNotNull('zoom_meeting_id')
            ->limit(1000)
            ->where('is_done_record',0)
            ->get();
        foreach ($sessions as $session) {
            $meetingId = $session->zoom_meeting_id;

            $path = 'meetings/' . $meetingId . '/recordings';
            $response = $this->zoomGet($path);
            $meeting = json_decode($response->body(), true);
            $this->info('body .'.$response->body());

            if (isset($meeting['id']) && isset($meeting['recording_files'])) {
                $accessToken = $this->generateZoomToken();
                $records = $this->getZoomMeetingRecord($meeting);
                $k = $session->recordedFile()->exists() ? $session->recordedFile()->count() + 1 : 1;
                foreach ($records as $record) {
                    $recordId = $record['id'];
                    $downloadUrl = $record['download_url'] . '?access_token=' . $accessToken;
                    $file = Http::get($downloadUrl)->getBody();
                    $filename = $session->subject_name . '-' . $k . "." . $record['file_type'];
                    $filePath = $session->id . '/' . $filename;
                    $this->saveMeetingRecordToS3($session, $file, $filePath, $filename, $record);
                    $k++;
                }
                $this->deleteZoomRecordings($meetingId);
            }
            $session->update(['is_done_record'=>1]);
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
