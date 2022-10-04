<?php

namespace App\OurEdu\Profile\Requests\Api;

use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;

class UpdatePasswordRequest extends BaseApiParserRequest
{
    public function rules()
    {
        $rules['attributes.old_password'] = 'nullable';
        $rules['attributes.password'] = 'required|min:8|confirmed';

        return $rules;
    }
}
