<?php


namespace App\OurEdu\GeneralQuizzes\Homework\Requests;

use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CreateHomeWorkRequest extends BaseApiParserRequest
{
    protected $subjects = [];
    protected $sections = [];

    public function rules()
    {
        return [
            'attributes.start_at' => 'required|date_format:"Y-m-d H:i:s|before:attributes.end_at|after:' . now(),
            'attributes.end_at' => 'required|date_format:"Y-m-d H:i:s|after:attributes.start_at',
            'attributes.subject_id' => [
                'required', Rule::in($this->subjects),
            ],
            'attributes.subject_sections' => [
                'required', 'array', Rule::in($this->sections)
            ],
            'attributes.random_question' => 'required|boolean',
            'attributes.title' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'attributes.subject_id.in' => trans('general_quizzes.Invalid subject,please select from instructor\'s assigned subjects'),
            'attributes.subject_sections.in' => trans('general_quizzes.Invalid subject section')
        ];
    }

    protected function prepareForValidation()
    {
        if ($user = Auth::guard('api')->user()) {
            $this->subjects = $user->schoolInstructorSubjects()->pluck('subjects.id')->toArray() ?? [];
            $this->sections = SubjectFormatSubject::where('subject_id', $this->validationData()["attributes"]['subject_id'] ?? -1)->pluck('id')->toArray();
        }
    }
}
