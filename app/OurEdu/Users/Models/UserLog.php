<?php

namespace App\OurEdu\Users\Models;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class UserLog extends ShouldBeStored
{
    protected $table = 'stored_events';

    protected $dates = ['created_at'];
}
