<?php

namespace App\Modules\Users\Transformers;

use App\Modules\Users\Models\PasswordReset;
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
