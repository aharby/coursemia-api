<?php

namespace App\Modules\BaseApp\Api\Requests;

use App\Http\FormRequest;

final class BaseApiTokenRequest extends FormRequest
{
    public function rules()
    {
        return [];
    }
}
