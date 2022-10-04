<?php


namespace App\OurEdu\Subjects\SME\Requests;


use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;
use App\OurEdu\Options\Enums\OptionsTypes;
use Illuminate\Validation\Rule;

class CloneSubjectRequest extends BaseApiParserRequest
{
    public function rules()
    {
        return [
            'attributes.name' => 'nullable',
            'attributes.academic_years'=> [
                'nullable',
                Rule::exists('options','id')->where(function ($query) {
                    $query->where('type', OptionsTypes::ACADEMIC_YEAR);
                }),
            ],
        ];
    }
}
