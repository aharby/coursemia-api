<?php

namespace App\OurEdu\BaseNotification\Controller;

use App\OurEdu\BaseNotification\MailNotification\MailNotification;

class SendController
{
    public function sendMail()
    {
        (new MailNotification())->send([
            'data' => [
                'data' => request()->toArray()
            ],
            'user_type' => 'guest',
            'view' => 'guest',
            'subject' => 'new call action'

        ],['marketing@ikcedu.net']);

        return response()->json([
            'meta' => [
                'message' => 'success'
            ]
        ],200);
    }
}
