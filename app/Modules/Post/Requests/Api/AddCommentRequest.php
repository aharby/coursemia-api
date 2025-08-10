<?php

namespace App\Modules\Courses\Requests\Api;

use App\Enums\StatusCodesEnum;
use Illuminate\Contracts\Validation\Validator;
use App\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class AddCommentRequest extends FormRequest
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
            'comment'               => 'required',
            'post_id'               => 'required|exists:posts,id'
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
