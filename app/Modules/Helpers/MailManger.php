<?php

namespace App\Modules\Helpers;

use App\Modules\Users\Admin\Mail\RegisterNotification;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Log;

class MailManger extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;


    private $userType;

    private $data = [];

    private $singleData = [];

    /**
     * Prepare new Message data.
     * @param array $data
     * @return void
     */

    public function prepareMail(array $data)
    {
        if (! isset($data['user_type'])) {
            $data['user_type'] = 'default';
        }

        if (! isset($data['subject'])) {
            $data['subject'] = env('APP_NAME', 'Our Edu');
        }

        if (! isset($data['view'])) {
            Log::error('View file should be exists');
        }

        if (! isset($data['data'])) {
            $data['data'] = [];
        }

        if (! isset($data['emails'])) {
            Log::error('Email file should be exists');
        }

        if (! is_array($data['emails'])) {
            $data['emails'] = [$data['emails']];
        }

        $this->data[] = $data;

        return $this;
    }

    /**
     * Handel Emails
     */
    public function handle()
    {
        try {
            if (env('MAIL_ENABLE', 1)) {
                foreach ($this->data as $data) {
                   Mail::to($data['emails'])->send(new MailTemplate($data['user_type'], $data['view'], $data['data'], $data['subject']));
                }
            }
        } catch (Exception $e) {
            Log::error($e);
        }
    }
}
