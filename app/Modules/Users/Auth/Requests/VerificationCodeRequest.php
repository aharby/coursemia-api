<?php

namespace App\Modules\Users\Auth\Requests;

use Illuminate\Contracts\Validation\Validator;
use App\Http\FormRequest;
use Illuminate\Http\JsonResponse;

class VerificationCodeRequest extends FormRequest
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
            'phone_number'      => 'required|exists:users,phone',
            'country_code'      => 'required|exists:countries,country_code',
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
