<?php

namespace App\Modules\BaseApp\Api;

use Bitstone\GuzzleWrapper\Http;

use Zttp\Zttp;

class BaseJsonApiHandler
{
    public static function makeRequest($endPoint, $method = 'post', $paramData = [], $isReturnJsonApi = true)
    {
        $baseApiUrl = env('TRACKER_BASE_API_URL', 'http://localhost:8080');

        $url = "$baseApiUrl{$endPoint}";
        $response = Zttp::withHeaders([
            'Content-Type' => 'application/vnd.api+json',
            'Accept' => 'application/vnd.api+json',
            'app_key' => env('TRACKER_APP_KEY', 'Oi4HSYHNLWkHzDY_0Nq8H093wDTvVTclHvYWNL_Pdpxq5jNxkXFuJyS_E0y0dZWyQ3qa-P2YGgQ=')
        ])->$method($url, $paramData);
        return $response->body();
    }
}
