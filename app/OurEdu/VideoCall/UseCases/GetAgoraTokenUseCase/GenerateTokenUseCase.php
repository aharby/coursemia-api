<?php


namespace App\OurEdu\VideoCall\UseCases\GetAgoraTokenUseCase;

use App\OurEdu\Agora\src\RtcTokenBuilder;

class GenerateTokenUseCase implements GenerateTokenInterface
{
    public function getToken(string $channel)
    {
        $role = RtcTokenBuilder::RoleAttendee;
        $expireTimeInSeconds = 3600;
        $uid = null;
        $currentTimestamp = (new \DateTime("now", new \DateTimeZone('UTC')))->getTimestamp();
        $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;
        return RtcTokenBuilder::buildTokenWithUid(env('VIDEO_APP_AGORA_APP_ID'), env('VIDEO_APP_AGORA_CUSTOMER_CERTIFICATE'), $channel, $uid, $role, $privilegeExpiredTs);
    }
}
