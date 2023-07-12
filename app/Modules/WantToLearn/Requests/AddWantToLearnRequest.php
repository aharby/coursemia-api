<?php

namespace App\Modules\WantToLearn\Requests;

use App\Enums\StatusCodesEnum;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class AddWantToLearnRequest extends FormRequest
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
            'lecture_id'               => 'required|exists:course_lectures,id'
        ];
    }

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
