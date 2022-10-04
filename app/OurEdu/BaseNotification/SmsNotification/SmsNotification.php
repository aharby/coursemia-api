<?php

namespace App\OurEdu\BaseNotification\SmsNotification;

use App\OurEdu\BaseNotification\Sms;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\App;

class SmsNotification implements SmsNotificationInterface
{

    /**
     * @var Client
     */
    private $client;
    /**
     * @var mixed
     */
    private $message;
    /**
     * @var mixed|null
     */

    private $baseUrl = 'http://api.yamamah.com/SendSMSV2';
    private $username;
    private $password;
    private $tagName;
    private $variableList = '';
    private $replacementList = '';
    /**
     * @var array
     */
    private $mobiles;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * @param array $notification
     * @param array $mobile
     */
    public function send(array $notification, array $mobile)
    {
        $this->mobiles = $mobile;
        $this->message = $notification['message'];
        $this->username = config('services.yamamah.username');
        $this->password = config('services.yamamah.password');
        $this->tagName = config('services.yamamah.tagName');
        $this->handleMobiles();
    }

    private function handleMobiles()
    {
        foreach ($this->mobiles as $mobile) {
            $this->sendRequest($mobile);
        }
    }

    private function sendRequest($mobile)
    {
        if (App::environment('production')) {
            $response = $this->client->get(
                $this->baseUrl . '?username=' . $this->username . '&password=' . $this->password . '&Tagname=' . $this->tagName . '&RecepientNumber='
                . $mobile
                . '&VariableList=' . $this->variableList . '&ReplacementList=' . $this->replacementList . '&Message=' . $this->message .
                '&SendDateTime=0&EnableDR=true&SentMessageID=true'
            );
        }
        $this->saveSms($response ?? null, $mobile);
    }

    private function saveSms($response, $mobile)
    {
        Sms::create(
            [
                'response' => $this->getResponse($response),
                'message' => $this->message,
                'mobile' => $mobile
            ]
        );
    }

    private function getResponse($response)
    {
        if (is_null($response)) {
            return 'test';
        }
        return json_encode($response->getBody()->getContents());
    }
}
