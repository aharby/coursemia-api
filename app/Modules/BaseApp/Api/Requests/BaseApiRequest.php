<?php

namespace App\Modules\BaseApp\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class BaseApiRequest extends FormRequest
{
    public function rules()
    {
        return [];
    }
}
