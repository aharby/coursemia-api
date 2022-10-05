<?php

namespace App\Modules\BaseApp\Api;

use Bitstone\GuzzleWrapper\Http;
use Illuminate\Support\Facades\Log;
use Zttp\Zttp;

class BaseApiHandler
{
    public function __construct()
    {
        $this->baseApiUrl = 'http://82.129.197.84:8080';
    }

    public static function makeRequest($endPoint, $paramData = [], $isReturnJson = true, $method = 'post')
    {
        $baseApiUrl = env('FEDEX_BASE_API_URL', 'http://82.129.197.84:8080');


        $fedexPasswordMd5 = md5(env('FEDEX_PASSWORD', 'ja8ey&213;asljd'));

        $paramData['accountNo'] = env('FEDEX_ACCOUNT_NO', '13');
        $paramData['password'] = $fedexPasswordMd5;

        $url = "$baseApiUrl/{$endPoint}";
        $response = Zttp::asFormParams()->$method($url, $paramData);


        if ($isReturnJson) {
            $return = $response->json();
            $handelArray = [];
            $handelArray['data'] = [];


            foreach ($return as $key => $value) {
                if ($key != 'response_code' && $key != 'response_message') {
                    $handelArray['data'][$key] = $value;
                }
            }
            $handelArray['code'] = $return['response_code'];
            $handelArray['message'] = $return['response_message'];

            if ($handelArray['code'] != '200') {
                $jsonParams = json_encode($paramData);
                Log::error("api endpoint:  {$url} code: {$handelArray['code']} error message: {$handelArray['message']} params: {$jsonParams}");
            }
            return $handelArray;
        }
        return $response->body();
    }

    public static function makeLIntgraRequest($endPoint, $paramData = [], $method = 'post')
    {
        $baseApiUrl = env('LUMEN_INTGRA_API_URL');

        try {
            $url = "$baseApiUrl/{$endPoint}";
            $response = Zttp::asJson()->$method($url, $paramData);


            $json = $response->json();
            $handelArray = [];
            $handelArray['data'] = [];


            $handelArray['code'] = $response->status();
            $handelArray['message'] = '';

            if (!$response->isSuccess()) {
                $handelArray['data'] = $json['errors'];
                $jsonParams = json_encode($paramData);
                $errors = implode(',', $json['errors']);
                Log::error("api endpoint:  {$url} code: {$handelArray['code']} error message: {$handelArray['message']}  errors: {$errors} params: {$jsonParams}");
            } else {
                $handelArray['data'] = $json['data'];
            }
            return $handelArray;
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
        }
    }
}
