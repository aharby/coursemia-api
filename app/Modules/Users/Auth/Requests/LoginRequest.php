<?php

namespace App\Modules\Users\Auth\Requests;

use Illuminate\Contracts\Validation\Validator;
use App\Http\FormRequest;
use Illuminate\Http\JsonResponse;

class LoginRequest extends FormRequest
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
            'phone_number'      => 'required_without:email|exists:users,phone',
            'country_code'      => 'required_with:phone_number|exists:countries,country_code',
            'email'              => 'required_without:phone_number|exists:users,email',
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
