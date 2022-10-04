<?php


namespace App\OurEdu\VideoCall\UseCases\GetAgoraTokenUseCase;


interface GenerateTokenInterface
{
    public function getToken(string $channel);
}
