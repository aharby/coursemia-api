<?php

namespace App\OurEdu\BaseNotification\MailNotification\NotificationMailTemplate;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;

class NotificationMailTemplate extends Mailable implements ShouldQueue
{
    private $userType;
    private $viewTemplate;
    private $data;
    private $subjectEmail;

    /**
     * Create a new message instance.
     *
     * @param string $userType
     * @param string $view
     * @param array $data
     * @param string $subject
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
