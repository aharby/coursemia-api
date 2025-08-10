<?php

namespace App\Modules\BaseApp\Api\Requests;

use App\Http\FormRequest;

final class BaseApiRequest extends FormRequest
{
    public function rules()
    {
        return [];
    }
}
