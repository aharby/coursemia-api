<?php

namespace App\Modules\Users\Auth\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;

class ApiRegisterRequest extends FormRequest
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
            'full_name'         => 'required|max:255',
            'phone_number'      => 'required|unique:users,phone',
            'email_address'     => 'required|unique:users,email',
            'country_id'        => 'required|exists:countries,id',
            'refer_code'        => 'nullable|exists:users,refer_code',
            'country_code'      => 'required|exists:countries,country_code',
            'password' => ['required', 
                    'regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{9,}$/']   
        ];
    }

    public function messages()
    {
        return [
            'password.regex' => __('auth.Password Regex'),
            'email_address.unique' => 'The :attribute is already taken. Please login.',
            'phone_number.unique'  => 'The :attribute is already taken. Please login.',
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
