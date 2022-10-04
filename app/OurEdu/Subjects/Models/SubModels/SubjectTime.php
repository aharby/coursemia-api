<?php

namespace App\OurEdu\Subjects\Models\SubModels;

use Spatie\EventSourcing\StoredEvents\Models\EloquentStoredEvent;

class SubjectTime extends EloquentStoredEvent
{
    protected $table = 'subject_times';

    public $fillable = [
        'subject_id',
        'student_id',
        'timable_type',
        'timable_id',
        'start_time',
        'time',
    ];

    public function timable()
    {

        return $this->morphTo();
    }
}
