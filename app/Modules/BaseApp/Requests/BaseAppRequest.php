<?php

namespace App\Modules\BaseApp\Requests;

use App\Http\FormRequest;

class BaseAppRequest extends FormRequest
{
    public function rules(){
        return [];
    }
}
