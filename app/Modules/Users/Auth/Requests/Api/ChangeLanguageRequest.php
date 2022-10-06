<?php

namespace App\Modules\Users\Auth\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Modules\BaseApp\Api\Requests\BaseApiParserRequest;

class ChangeLanguageRequest extends FormRequest
{
    public function rules()
    {
        return [
            'attributes.lang_slug'=> ['required', Rule::in(langs())]
        ];
    }
}
