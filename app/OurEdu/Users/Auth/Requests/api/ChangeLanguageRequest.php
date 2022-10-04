<?php

namespace App\OurEdu\Users\Auth\Requests\Api;

use Illuminate\Validation\Rule;
use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;

class ChangeLanguageRequest extends BaseApiParserRequest
{
    public function rules()
    {
        return [
            'attributes.lang_slug'=> ['required', Rule::in(langs())]
        ];
    }
}
