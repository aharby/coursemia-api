<?php

namespace App\OurEdu\Users\Repository;

use App\OurEdu\Users\User;

class FirebaseTokenRepository implements FirebaseTokenRepositoryInterface
{
    public function store(User $user, array $data)
    {
        return $user->firebaseTokens()->create($data);
    }

    public function delete(User $user, array $data)
    {
        return $user->firebaseTokens()->where($data)->delete();
    }
}
