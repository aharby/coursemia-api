<?php

namespace App\Modules\Users\Auth\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SendResetMailRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'attributes.email' => 'required|email|exists:users,email',
        ];
    }
}
