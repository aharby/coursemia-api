<?php

namespace App\OurEdu\Users\Transformers;

use App\OurEdu\Users\Models\PasswordReset;
use League\Fractal\Scope;
use League\Fractal\TransformerAbstract;

class ResetPasswordLinkTransformer extends TransformerAbstract
{

    public function transform($data)
    {
        return [
            'id' => \Str::uuid(),
            'url' => $data['url'],
            'token' => $data['token'],
        ];
    }
}
