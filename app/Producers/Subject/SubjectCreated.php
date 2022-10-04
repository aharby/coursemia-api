<?php

namespace App\Producers\Subject;

use RabbiteventsMod\Events\Event\Publishable;
use RabbiteventsMod\Events\Event\ShouldPublish;
use RabbiteventsMod\Events\Event\Testing\PublishableEventTesting;

class SubjectCreated implements ShouldPublish
{
    use Publishable;
    use PublishableEventTesting;

    private $subject;

    public function __construct($subject)
    {
        $this->subject = $subject;
    }

    public function publishEventKey(): string
    {
        return 'subject.create';
    }

    public function toPublish(): array
    {
        return $this->subject;
    }
}
