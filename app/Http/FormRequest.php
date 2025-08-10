<?php

namespace App\Http;

use App\Enums\StatusCodesEnum;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;

class FormRequest extends \Illuminate\Foundation\Http\FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        $response = new JsonResponse([
            'data' => (object)[],
            'message' => $validator->errors()->first(),
            'status_code' => StatusCodesEnum::FAILED,
        ], 422);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}