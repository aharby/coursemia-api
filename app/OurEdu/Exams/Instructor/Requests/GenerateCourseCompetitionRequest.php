<?php

namespace App\OurEdu\Exams\Instructor\Requests;

use App\BokDoc\Enums\ReplacedMessageEnum;
use App\OurEdu\BaseApp\Enums\ReplacedValidationMessageEnum;
use App\OurEdu\Courses\Enums\CourseEnums;
use App\OurEdu\Options\Option;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\OurEdu\Exams\Enums\ExamEnums;
use App\OurEdu\Options\Enums\OptionsTypes;
use App\OurEdu\BaseApp\Requests\BaseAppRequest;
use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use Illuminate\Validation\ValidationException;

class GenerateCourseCompetitionRequest extends BaseApiParserRequest
{
    protected $sections = [];
    private $difficultyLevels = [];
    private $allowedQuestionsCount = [];
    private  $students = [];

    public function rules()
    {
        if($this->route('course')->type !== CourseEnums::SUBJECT_COURSE) {

            return [];
        }
            return [
            'attributes.subject_format_subject_ids'=> ['required', 'array', Rule::in($this->sections)],
            'attributes.number_of_questions'=> ['required', Rule::in($this->allowedQuestionsCount)],
            'attributes.difficulty_level'=> ['required', Rule::in($this->difficultyLevels)],
            'attributes.students'=> ['required','array','max:30','min:2',Rule::in($this->students)],
            'attributes.start_time' => 'required|date|after:' . Carbon::now()->addMinutes(10),
            'attributes.end_time' => 'required|date|after:attributes.start_time',
            ];
    }

    protected function prepareForValidation()
    {
        if ($this->route('course')->type == CourseEnums::SUBJECT_COURSE) {
            $this->difficultyLevels = Option::where('type', OptionsTypes::RESOURCE_DIFFICULTY_LEVEL)->pluck('id')->toArray();
            $this->allowedQuestionsCount = allowedQuestionsCountForExam();
            $this->sections = $this->route('course')->subject?->subjectFormatSubject->pluck('id')->toArray() ?? [];
            $this->students = $this->route('course')->students->pluck('id')->toArray();
        }
    }

    public function messages()
    {
        return [
            'attributes.subject_format_subject_ids.required' => trans('validation.subject_format_subject_ids.required'),
            'attributes.difficulty_level.required' => trans('validation.difficulty_level.required'),
            'attributes.number_of_questions.required' => trans('validation.number_of_questions.required'),
            'attributes.number_of_questions.in' => trans('validation.number_of_questions.required'),
            'attributes.students.min' => trans('validation.students.min',['num'=>2]),
            'attributes.students.max' => trans('validation.students.max',['num'=>30]),
        ];
    }

}
