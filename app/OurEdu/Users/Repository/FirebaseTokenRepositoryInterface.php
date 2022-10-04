<?php

namespace App\OurEdu\Users\Repository;

use App\OurEdu\Users\User;

interface FirebaseTokenRepositoryInterface
{
    public function store(User $user, array $data);

    public function delete(User $user, array $data);
}
