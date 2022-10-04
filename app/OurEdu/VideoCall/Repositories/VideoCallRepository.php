<?php


namespace App\OurEdu\VideoCall\Repositories;

use App\OurEdu\VideoCall\Models\VideoCallRequest;
use Illuminate\Support\Str;

class VideoCallRepository implements VideoCallRepositoryInterface
{
    public function create($user_student, $parent)
    {
        $video_call_request = new VideoCallRequest();
        $video_call_request->from_user_id = $parent->id;
        $video_call_request->student_id = $user_student->id;
        $video_call_request->channel = Str::uuid();
        $video_call_request->to_user_id = $user_student->student->classroom->branch->supervisor_id;
        $video_call_request->save();
        return $video_call_request;
    }

    public function updateVideoCallStatus($data)
    {
        $call_request = VideoCallRequest::find($data['call_request_id']);
        $call_request->status = $data['status'];
        $call_request->save();
        return $call_request;
    }

    public function cancelVideoCall($data)
    {
        $video_call_request = VideoCallRequest::find($data['request_id']);
        $video_call_request->status = VideoCallRequest::STATUS_CANCELLED;
        $video_call_request->save();
        return $video_call_request;
    }
}
