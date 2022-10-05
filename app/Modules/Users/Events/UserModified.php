<?php

namespace App\Modules\Users\Events;

use Illuminate\Support\Facades\Auth;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class UserModified extends ShouldBeStored
{
    use SerializesModels, Dispatchable;

    /**
     * @var array
     */
    public $userAttributes;
    public $user;
    public $by;
    public $action;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(array $userAttributes, array $user, $action = 'User created')
    {
        $this->by = Auth::id();
        $this->userAttributes = $userAttributes;
        $this->action = $action;
        $this->user = $user;
    }
}
