<?php

namespace App\Modules\BaseApp\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class BaseApiTokenRequest extends FormRequest
{
    public function rules()
    {
        return [];
    }
}
