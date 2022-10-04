<?php

namespace App\OurEdu\Exams\User\Requests;

use App\OurEdu\BaseApp\Enums\ReplacedValidationMessageEnum;
use App\OurEdu\Exams\Enums\AptitudeEnums;
use App\OurEdu\Options\Option;
use App\OurEdu\Subjects\Models\Subject;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\OurEdu\Options\Enums\OptionsTypes;
use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use Illuminate\Validation\ValidationException;

class GenerateExamRequest extends BaseApiParserRequest
{
    protected $subjects = [];
    protected $sections = [];

    public function rules()
    {
        // if complete aptitude test, no need to check difficulty_levels and allowed_questions_count
        return [
            'attributes.subject_id' => ['required', Rule::in($this->subjects)],
            'attributes.subject_format_subject_ids'=> ['required', 'array', Rule::in($this->sections)],
            'attributes.number_of_questions'=> ['required', Rule::in($this->allowed_questions_count)],
            'attributes.difficulty_level'=> ['required', Rule::in($this->difficulty_levels)],

        ];
    }

    protected function prepareForValidation()
    {
        $this->difficulty_levels = Option::where('type', OptionsTypes::RESOURCE_DIFFICULTY_LEVEL)->pluck('id')->toArray();

        $this->allowed_questions_count = allowedQuestionsCountForExam();

        if ($user = Auth::guard('api')->user()) {
            $this->subjects = $user->subjects()->pluck('subjects.id')->toArray() ?? [];

            $this->sections = SubjectFormatSubject::where('subject_id', $this->validationData()['attributes']['subject_id'] ?? -1)->pluck('id')->toArray();
        }
    }

    public function messages()
    {
        return [
            'attributes.subject_id.required' => trans('validation.subject_id.required'),
            'attributes.subject_format_subject_ids.required' => trans('validation.subject_format_subject_ids.required'),
            'attributes.difficulty_level.required' => trans('validation.difficulty_level.required'),
            'attributes.number_of_questions.required' => trans('validation.number_of_questions.required'),
            'attributes.number_of_questions.in' => trans('validation.number_of_questions.required'),
        ];
    }
}
