<?php

namespace App\OurEdu\Profile\Transformers;

use App\OurEdu\Users\User;
use League\Fractal\TransformerAbstract;

class InvitationSenderTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [
    ];

    public function transform(User $user)
    {
        $transformedData = [
            'id' => (int) $user->id,
            'name' => (string) $user->name,
            'profile_picture' => (string) imageProfileApi($user->profile_picture),
        ];

        return $transformedData;
    }

}
