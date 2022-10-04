<?php

namespace App\OurEdu\Exams\Student\Requests;

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
    protected $allowedQuestionsCount;
    private $difficultyLevels;

    public function rules()
    {
        // if complete aptitude test, no need to check difficulty_levels and allowed_questions_count
        if (
            Subject::find($this->validationData()['attributes']['subject_id'])->is_aptitude &&
            (SubjectFormatSubject::whereIn('id', $this->validationData()['attributes']['subject_format_subject_ids'])
                    ->pluck('slug')->toArray()
                == array(AptitudeEnums::QUANTITATIVE_SECTION, AptitudeEnums::VERBAL_SECTION))
        ) {
            return [
                'attributes.subject_id' => ['required', Rule::in($this->subjects)],
                'attributes.subject_format_subject_ids' => ['required', 'array', Rule::in($this->sections)],
            ];
        }


        return [
            'attributes.subject_id' => ['required', Rule::in($this->subjects)],
            'attributes.subject_format_subject_ids' => ['required', 'array', Rule::in($this->sections)],
            'attributes.number_of_questions' => ['required', Rule::in($this->allowedQuestionsCount)],
            'attributes.difficulty_level' => ['required', Rule::in($this->difficultyLevels)],

        ];
    }

    /**
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->difficultyLevels = Option::where('type', OptionsTypes::RESOURCE_DIFFICULTY_LEVEL)
            ->pluck('id')->toArray();

        $this->allowedQuestionsCount = allowedQuestionsCountForExam();

        if ($user = Auth::guard('api')->user()) {
            $this->subjects = $user->student->subjects()->pluck('subjects.id')->toArray() ?? [];

            $this->sections = SubjectFormatSubject::where(
                'subject_id',
                $this->validationData()['attributes']['subject_id'] ?? -1
            )->pluck('id')->toArray();
        }
    }

    /**
     * @return array
     */
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
