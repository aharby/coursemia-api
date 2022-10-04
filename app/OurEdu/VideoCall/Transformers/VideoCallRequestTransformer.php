<?php


namespace App\OurEdu\VideoCall\Transformers;


use App\OurEdu\VideoCall\Models\VideoCallRequest;
use League\Fractal\TransformerAbstract;

class VideoCallRequestTransformer extends TransformerAbstract
{
    public function transform(VideoCallRequest $videoCallRequest)
    {
        return [
            'id' => $videoCallRequest->id,
            'from_user_id' => $videoCallRequest->from_user_id,
            'to_user_id' => $videoCallRequest->to_user_id,
            'student_id' => $videoCallRequest->student_id,
            'status' => $videoCallRequest->status,
            'supervisor_leave_time' => $videoCallRequest->status,
            'parent_leave_time' => $videoCallRequest->status,
        ];
    }
}
