<?php

namespace App\Modules\Users\Auth\Requests\Api;

use App\Modules\BaseApp\Api\Requests\BaseApiParserRequest;

class ResetUserPasswordCodeRequest extends BaseApiParserRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'attributes.identifier' => ['required']
        ];
    }
}
