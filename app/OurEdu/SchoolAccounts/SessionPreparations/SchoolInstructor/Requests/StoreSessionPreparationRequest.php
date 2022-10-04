<?php


namespace App\OurEdu\SchoolAccounts\SessionPreparations\SchoolInstructor\Requests;


use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;
use Illuminate\Validation\ValidationException;

class StoreSessionPreparationRequest extends BaseApiParserRequest
{
    protected function prepareForValidation()
    {
        $data = $this->validationData()['attributes'];
        if (!empty($data['objectives']) && !empty($data['section_id'])) {
            throw ValidationException::withMessages([
                'attributes.section_id' => trans('validation.accept_one_attribute', [
                    'attribute' => trans('validation.attributes.attributes.section_id'),
                    'value' => trans('validation.attributes.attributes.objectives'),
                ]),
                'attributes.objectives' => trans('validation.accept_one_attribute', [
                    'attribute' => trans('validation.attributes.attributes.objectives'),
                    'value' => trans('validation.attributes.attributes.section_id'),
                ]),
            ]);
        }
    }

    public function rules()
    {
        return [
            'attributes.internal_preparation' => 'present|nullable',
            'attributes.pre_Learning' => 'present|nullable',
            'attributes.introductory' => 'present|nullable',
            'attributes.application' => 'present|nullable',
            'attributes.evaluation' => 'present|nullable',
            'attributes.section_id' => 'required_without:attributes.objectives|exists:subject_format_subject,id',
            'attributes.objectives' => 'required_without:attributes.section_id',
        ];
    }
}
