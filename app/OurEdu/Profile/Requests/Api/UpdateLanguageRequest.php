<?php

namespace App\OurEdu\Profile\Requests\Api;

use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;
use Illuminate\Validation\Rule;

class UpdateLanguageRequest extends BaseApiParserRequest
{
    public function rules()
    {
        $rules['attributes.language'] = [Rule::in(langs())];

        return $rules;
    }
}
