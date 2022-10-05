<?php

namespace App\Modules\Users\Repository;

use App\Modules\Users\User;

interface FirebaseTokenRepositoryInterface
{
    public function store(User $user, array $data);

    public function delete(User $user, array $data);
}
