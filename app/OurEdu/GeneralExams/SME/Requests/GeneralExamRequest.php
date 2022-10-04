<?php

namespace App\OurEdu\GeneralExams\SME\Requests;

use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;
use App\OurEdu\Options\Enums\OptionsTypes;
use App\OurEdu\Options\Option;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class GeneralExamRequest extends BaseApiParserRequest
{
    protected $subjects = [];

    public function rules()
    {
        return [
            'attributes.name' => ['required'],
            'attributes.date' => ['required', 'date', 'after:now'],
            'attributes.start_time' => ['required', 'date_format:H:i:s', 'before:attributes.end_time'],
            'attributes.end_time' => ['required', 'date_format:H:i:s', 'after:attributes.start_time'],
            'attributes.subject_id' => ['required', 'integer', Rule::in($this->subjects)],
            'attributes.difficulty_level_id' => ['required', Rule::in($this->difficulty_levels)],

        ];
    }

    protected function prepareForValidation()
    {
        $this->difficulty_levels = Option::where('type', OptionsTypes::RESOURCE_DIFFICULTY_LEVEL)->pluck('id')->toArray();

        if ($user = Auth::guard('api')->user()) {
            $this->subjects = $user->managedSubjects()->pluck('id')->toArray();
        }
    }
}
