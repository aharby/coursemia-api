<?php

namespace App\Modules\BaseApp\Api;


use Zttp\Zttp;

class BaseJsonAgoraHandler
{
    public static function makeRequest($endPoint, $method = 'post', $paramData = [], $userToken = null)
    {
        $baseApiUrl = env('AGORA_EDU_BASE_API', 'https://api.agora.io/edu/v1/apps');
        $agoraAppId = env('AGORA_APP_ID', '68dfaf00c26140ed872b7a2d09e59397');
        $agoraAuthToken = env('AGORA_AUTH_TOKEN', 'Basic ODBhMDBiZWQ4OTY5NDFjZTllN2NmNWU2MmE5MGZhNTY6YjE2NWQ5NDI3MmMwNDlhMWE0ODNiM2E3YzA1YWQyNDg=');
//        https://api.agora.io/edu/v1/apps/21aafae6820b453cab4d23799b6395f1/room/entry'
        $url = "$baseApiUrl/{$agoraAppId}/{$endPoint}";
        $headers = [
            'Authorization' =>$agoraAuthToken,
        ];
        if ($userToken){
            $headers['token'] = $userToken;
        }
        $response = Zttp::withHeaders($headers)->$method($url, $paramData);
        return $response->json();
    }
}
