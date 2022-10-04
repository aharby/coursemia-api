<?php


namespace App\OurEdu\VideoCall\UseCases\VideoCallUseCase;


use App\OurEdu\Users\User;

interface VideoCallUseCaseInterface
{
    public function makeCallRequest($data);
    public function updateVideoCallStatus($data);
    public function cancelVideoCall($data);
    public function LeaveVideoCall($videoCallRequestId, User $user);
}
