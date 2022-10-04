<?php

namespace App\OurEdu\Helpers;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MailTemplate extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;


    private $userType;
    private $viewTemplate;
    private $data;
    private $subjectEmail;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $userType, string $view, array $data, string $subject)
    {
        $this->userType = $userType;
        $this->viewTemplate = $view;
        $this->data = $data;
        $this->subjectEmail = $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(env('MAIL_FROM_EMAIL', 'no-reply@mail.com'))->with($this->data)->subject($this->subjectEmail)
            ->markdown('emails.'.$this->userType.'.'.$this->viewTemplate);
    }
}
