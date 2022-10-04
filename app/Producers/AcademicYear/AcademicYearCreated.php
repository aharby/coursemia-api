<?php

namespace App\Producers\AcademicYear;

use RabbiteventsMod\Events\Event\Publishable;
use RabbiteventsMod\Events\Event\ShouldPublish;
use RabbiteventsMod\Events\Event\Testing\PublishableEventTesting;

class AcademicYearCreated implements ShouldPublish
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
        return 'academic_year.create';
    }

    public function toPublish(): array
    {
        return $this->academicYear;
    }
}
