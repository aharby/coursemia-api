<?php

namespace App\Modules\Users\Traits;

use App\Modules\Users\User;
use App\Modules\Users\UserEnums;
use App\Modules\Invitations\Models\Invitation;

trait Invitable
{
    public function sentInvitations()
    {
        return $this->hasMany(Invitation::class, 'sender_id');
    }

    public function receivedInvitations()
    {
        return $this->hasMany(Invitation::class, 'receiver_id', 'id');
    }

    public function invitations()
    {
        return $this->morphMany(Invitation::class, 'invitable');
    }
}
