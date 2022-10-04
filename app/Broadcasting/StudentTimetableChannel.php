<?php

namespace App\Broadcasting;

use App\OurEdu\BaseApp\Api\Traits\ApiResponser;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Users\Transformers\UserAuthTransformer;
use App\OurEdu\Users\User;

class StudentTimetableChannel
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
     * @param  User  $user
     * @return array
     */
    public function join(User $user)
    {
        return  [
            'id' => (int) $user->id,
            'first_name' => (string) $user->first_name,
            'last_name' => (string) $user->last_name,
            'profile_picture' => (string) imageProfileApi($user->profile_picture),
        ];
    }
}
