<?php

namespace App\Producers\AcademicYear;

use Illuminate\Foundation\Events\Dispatchable;
use RabbiteventsMod\Events\Event\Publishable;
use RabbiteventsMod\Events\Event\ShouldPublish;
use RabbiteventsMod\Events\Event\Testing\PublishableEventTesting;

class AcademicYearUpdated implements ShouldPublish
{
    use Publishable;
    use PublishableEventTesting;

    private $academicYear;

    public function __construct($academicYear)
    {
        $this->academicYear = $academicYear;
    }

    public function publishEventKey(): string
    {
        return 'academic_year.update';
    }

    public function toPublish(): array
    {
        return $this->academicYear;
    }
}
