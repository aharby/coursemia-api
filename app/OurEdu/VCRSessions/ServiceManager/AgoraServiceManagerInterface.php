<?php

namespace App\OurEdu\VCRSessions\ServiceManager;

interface  AgoraServiceManagerInterface
{
    public function generateToken($channelName, $uuid);
}
