<?php


namespace App\OurEdu\VideoCall\UseCases\VideoCallUseCase;


use App\Events\VideoCallCancelEvent;
use App\Events\VideoCallEvent;
use App\Events\VideoCallStatusEvent;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Users\User;
use App\OurEdu\VideoCall\Models\VideoCallRequest;
use App\OurEdu\VideoCall\Repositories\VideoCallRepositoryInterface;
use App\OurEdu\VideoCall\UseCases\GetAgoraTokenUseCase\GenerateTokenInterface;

class VideoCallUseCase implements VideoCallUseCaseInterface
{
    private $videoCallRepository, $generateTokenUseCase;

    public function __construct(VideoCallRepositoryInterface $videoCallRepository,
                                GenerateTokenInterface $generateTokenUseCase
    )
    {
        $this->videoCallRepository = $videoCallRepository;
        $this->generateTokenUseCase = $generateTokenUseCase;
    }

    public function makeCallRequest($data)
    {
        $userStudent = User::find($data->user_id);
        $branch_id = $userStudent->branch_id;
        $video_call_request = $this->videoCallRepository->create($userStudent, auth()->user());
        broadcast(new VideoCallEvent(auth()->user(), $userStudent, $branch_id, $video_call_request));
        $useCase['status'] = 200;
        $useCase['video_call_request'] = $video_call_request;
        return $useCase;
    }

    public function updateVideoCallStatus($data)
    {
        $supervisor = auth()->user();
        $token = null;
        $call_request = $this->videoCallRepository->updateVideoCallStatus($data);
        if ($data['status'] == 'accepted') {
            $token = $this->generateTokenUseCase->getToken($call_request->channel);
        }
        broadcast(new VideoCallStatusEvent($supervisor, $data['status'], $supervisor->branch->id, $token, $call_request, $call_request->channel));
        $useCase['status'] = 200;
        $useCase['token'] = $token;
        $useCase['video_call_request'] = $call_request;
        $useCase['channel'] = $call_request->channel;
        return $useCase;
    }

    public function cancelVideoCall($data)
    {
        $video_call_request = $this->videoCallRepository->cancelVideoCall($data);
        $branch_id = Student::where('user_id', $video_call_request->student_id)->first()->classroom->branch->id;
        broadcast(new VideoCallCancelEvent(User::find($video_call_request->from_user_id), $branch_id));
        $useCase['status'] = 200;
        $useCase['video_call_request'] = $video_call_request;
        return $useCase;
    }

    public function LeaveVideoCall($videoCallRequestId, User $user)
    {
        $videoCallRequest = VideoCallRequest::find($videoCallRequestId);
        $parent = $videoCallRequest->from_user_id;
        $supervisor = $videoCallRequest->to_user_id;
        if ($user->id == $parent) {
            $videoCallRequest->update(['parent_leave_time' => now()]);
        } else if ($user->id == $supervisor) {
            $videoCallRequest->update(['supervisor_leave_time' => now()]);
        }
        $useCase['status'] = 200;
        return $useCase;
    }


}
