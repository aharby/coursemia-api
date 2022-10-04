<?php

namespace App\OurEdu\VCRSessions\ServiceManager;

use meteorTechnology\AgoraSDK\Agora;

class AgoraServiceManager implements AgoraServiceManagerInterface
{
    private $config;
    private $agora;

    public function __construct()
    {
        $this->setConfig();
        $this->agora = new Agora($this->config);
    }

    public function setConfig()
    {
        $this->config = [
            'debug'  => env('AGORA_APP_DEBUG', false),
            'id'     => env('AGORA_APP_ID','68dfaf00c26140ed872b7a2d09e59397'),
            'secret' => env('AGORA_APP_CERTIFICATE','Basic ODBhMDBiZWQ4OTY5NDFjZTllN2NmNWU2MmE5MGZhNTY6YjE2NWQ5NDI3MmMwNDlhMWE0ODNiM2E3YzA1YWQyNDg=')
        ];
    }

    public function generateToken($channelName, $uuid)
    {
        return $this->agora->token
                ->buildToken($channelName, $uuid);
    }
}
