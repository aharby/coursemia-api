<?php

declare(strict_types=1);

namespace App\Producers\GradeClass;

use RabbiteventsMod\Events\Event\Publishable;
use RabbiteventsMod\Events\Event\ShouldPublish;
use RabbiteventsMod\Events\Event\Testing\PublishableEventTesting;

class GradeCreatedSync implements ShouldPublish
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
        return 'grade.create.sync';
    }

    public function toPublish(): array
    {
        return $this->payload;
    }
}
