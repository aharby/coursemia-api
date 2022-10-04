<?php


namespace App\Broadcasting;


use App\OurEdu\Users\User;

class SchoolBranchChannel
{
    public function __construct()
    {

    }

    /**
     * @param User $user
     * @return array|bool
     */
    public function join(User $user)
    {
        return $user->toArray();
    }
}
