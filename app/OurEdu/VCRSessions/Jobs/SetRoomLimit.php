<?php


namespace App\OurEdu\VCRSessions\Jobs;

use App\OurEdu\BaseApp\Api\AgoraHandlerV2;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SetRoomLimit implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var VCRSession
     */
    private $vcrSession;

    /**
     * Create a new job instance
     * @param VCRSession $vcrSession
     */
    public function __construct(VCRSession $vcrSession)
    {
        $this->vcrSession = $vcrSession;
    }


    public function handle()
    {
        $paramData = [
            'roomName' => $this->vcrSession->subject_name,
            'roleConfig' =>
                [
                    'host' =>
                        [
                            'limit' => 1,
                        ],
                    'audience' =>
                        [
                            'limit' => 3000,
                        ],
                    'assistant' =>
                        [
                            'limit' => 0,
                        ],
                ],
            'roomProperties' =>
                [
                    'processes' =>
                        [
                            $this->vcrSession->room_uuid =>
                                [
                                    'type' => 1,
                                    'maxWait' => 4,
                                    'timeout' => 30,
                                    'repeatable' => true,
                                ],
                        ],
                    'handupStates' =>
                        [
                            'state' => 1,
                            'autoCovideo' => 0,
                        ],
                ],
        ];
        $response = AgoraHandlerV2::makeRequest('/rooms/' . $this->vcrSession->room_uuid . '/config/', 'post', $paramData);
        return 0;
    }
}
