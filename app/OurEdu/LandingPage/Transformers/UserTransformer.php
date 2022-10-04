<?php

namespace App\OurEdu\LandingPage\Transformers;

use App\OurEdu\Users\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    public function transform(User $user): array
    {
        return [
            'id' => (int)$user->id,
            'name' => $user->name,
            'profile_picture' => (string)imageProfileApi($user->profile_picture),
        ];
    }
}
