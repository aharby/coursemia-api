<?php

namespace App\OurEdu\Users\Traits;

use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\Invitations\Models\Invitation;

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
