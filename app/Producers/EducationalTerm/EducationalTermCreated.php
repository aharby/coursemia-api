<?php

namespace App\Producers\EducationalTerm;

use RabbiteventsMod\Events\Event\Publishable;
use RabbiteventsMod\Events\Event\ShouldPublish;
use RabbiteventsMod\Events\Event\Testing\PublishableEventTesting;

class EducationalTermCreated implements ShouldPublish
{
    use Publishable;
    use PublishableEventTesting;

    private $educationalTerm;

    public function __construct($educationalTerm)
    {
        $this->educationalTerm = $educationalTerm;
    }

    public function publishEventKey(): string
    {
        return 'semester.create';
    }

    public function toPublish(): array
    {
        return $this->educationalTerm;
    }
}
