<?php

namespace App\OurEdu\Profile\Transformers;

use App\OurEdu\Invitations\Models\Invitation;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class InvitationReceiverTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [
    ];

    public function transform(Invitation $invitation)
    {
        $transformedData = [
            'id' => (int) !is_null($invitation->receiver) ? $invitation->receiver->id : Str::uuid() ,
            'name' => (string) !is_null($invitation->receiver) ? $invitation->receiver->name : $invitation->receiver_email,
            'profile_picture' => (string) !is_null($invitation->receiver) ?  imageProfileApi($invitation->receiver->profile_picture) : dummyPicture(),
        ];

        return $transformedData;
    }

}
