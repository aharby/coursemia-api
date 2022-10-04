<?php

namespace App\OurEdu\Events\Models;


use Spatie\EventSourcing\StoredEvents\Models\EloquentStoredEvent;

class StudentStoredEvent extends EloquentStoredEvent
{
    protected $dates = ['created_at'];


    public function getEventPropertiesAttribute($value)
    {
        return json_decode($value,true);
    }
}
