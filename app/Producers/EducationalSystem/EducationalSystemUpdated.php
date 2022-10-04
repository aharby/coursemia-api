<?php

namespace App\Producers\EducationalSystem;

use Illuminate\Foundation\Events\Dispatchable;
use RabbiteventsMod\Events\Event\Publishable;
use RabbiteventsMod\Events\Event\ShouldPublish;
use RabbiteventsMod\Events\Event\Testing\PublishableEventTesting;

class EducationalSystemUpdated implements ShouldPublish
{
    use Publishable;
    use PublishableEventTesting;

    private $educationalSystem;

    public function __construct($educationalSystem)
    {
        $this->educationalSystem = $educationalSystem;
    }

    public function publishEventKey(): string
    {
        return 'educational_system.update';
    }

    public function toPublish(): array
    {
        return $this->educationalSystem;
    }
}
