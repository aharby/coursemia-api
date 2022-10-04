<?php

namespace App\OurEdu\Exams\Instructor\Requests;

use App\BokDoc\Enums\ReplacedMessageEnum;
use App\OurEdu\BaseApp\Enums\ReplacedValidationMessageEnum;
use App\OurEdu\Options\Option;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\OurEdu\Exams\Enums\ExamEnums;
use App\OurEdu\Options\Enums\OptionsTypes;
use App\OurEdu\BaseApp\Requests\BaseAppRequest;
use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use Illuminate\Validation\ValidationException;

class GenerateInstructorExamRequest extends BaseApiParserRequest
{
    protected $sections = [];

    public function rules()
    {
        return [
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
            $this->sections = SubjectFormatSubject::where('subject_id',$this->route('vcr_session')?->subject_id ?? -1)->pluck('id')->toArray();
        }
    }


}
