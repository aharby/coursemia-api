<?php

namespace App\OurEdu\Exams\Student\Requests\Competitions;

use App\OurEdu\Options\Option;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\OurEdu\Exams\Enums\ExamEnums;
use App\OurEdu\Options\Enums\OptionsTypes;
use App\OurEdu\BaseApp\Requests\BaseAppRequest;
use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;

class GenerateCompetitionRequest extends BaseApiParserRequest
{
    protected $subjects = [];
    protected $sections = [];

    public function rules()
    {
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

        $this->allowed_questions_count =  allowedQuestionsCountForExam();

        if ($user = Auth::guard('api')->user()) {
            $this->subjects = $user->student->subjects()->pluck('subjects.id')->toArray() ?? [];

            $this->sections = SubjectFormatSubject::where('subject_id', $this->validationData()['attributes']['subject_id'] ?? -1)->pluck('id')->toArray();
        }
    }
}
