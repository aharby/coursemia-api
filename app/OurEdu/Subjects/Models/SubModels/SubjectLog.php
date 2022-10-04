<?php

namespace App\OurEdu\Subjects\Models\SubModels;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class SubjectLog extends ShouldBeStored
{
    protected $table = 'subject_logs';

    protected $dates = ['created_at'];
}
