<?php

namespace App\OurEdu\Users\UseCases\CreateZoomUserUserCase;

use App\OurEdu\Users\User;

interface CreateZoomUserUseCaseInterface
{
    public function createUser(User $user) : array;

    public function changeProfilePicture(string $zoomUserId, string $picturePath = null);
}
