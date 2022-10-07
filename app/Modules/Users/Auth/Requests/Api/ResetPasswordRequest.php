<?php

namespace App\Modules\Users\Auth\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;

class ResetPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'country_code'      => 'required|exists:countries,phonecode',
            'phone_number'      => 'required|unique:users,phone',
            'password'          => 'required'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = new JsonResponse([
            'data' => (object)[],
            'message' => $validator->errors()->first(),
            'success' => (boolean)false
        ], 422);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
