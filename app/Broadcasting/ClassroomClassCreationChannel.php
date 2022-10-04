<?php

namespace App\Broadcasting;

use App\OurEdu\Users\User;

class ClassroomClassCreationChannel
{
    /**
     * Create a new channel instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     *
     * @param  \App\OurEdu\Users\User  $user
     * @return array|bool
     */
    public function join(User $user)
    {
        return $user->toArray();
    }
}
