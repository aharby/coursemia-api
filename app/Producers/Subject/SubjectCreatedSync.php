<?php

declare(strict_types=1);

namespace App\Producers\Subject;

use RabbiteventsMod\Events\Event\Publishable;
use RabbiteventsMod\Events\Event\ShouldPublish;
use RabbiteventsMod\Events\Event\Testing\PublishableEventTesting;

class SubjectCreatedSync implements ShouldPublish
{

    use Publishable;
    use PublishableEventTesting;

    private array $payload;

    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    public function publishEventKey(): string
    {
        return 'subject.create.sync';
    }

    public function toPublish(): array
    {
        return $this->payload;
    }
}
