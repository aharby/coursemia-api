<?php


namespace App\OurEdu\GeneralQuizzes\Homework\EducationalSupervisor\Requests;

use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateHomeworkRequest extends BaseApiParserRequest
{
    protected $subjects = [];
    protected $sections = [];

    public function rules()
    {

        $rules = [
            'attributes.end_at' => 'required|date_format:"Y-m-d H:i:s|after:start_date',
            'attributes.subject_id' => [
                'required', Rule::in($this->subjects)
            ],
            'attributes.subject_sections' => [
                'required', 'array', Rule::in($this->sections)
            ],
            'attributes.random_question' => 'required|boolean',
            'attributes.title' => 'required|string',
        ];
        if ($this->route('homework')) {
            $homework = $this->route('homework');
            if ((new Carbon($this->data['attributes']['start_at']))->format('Y-m-d H:i:s') != $homework->start_at) {
                $rules['start_at'] = 'required|date|before:end_at|after:' . now();
            }
        }
        return $rules;
    }

    public function messages()
    {
        return [
            'attributes.subject_id.in' => trans('general_quizzes.Invalid subject,please select from your assigned subjects that match with instructor\'s assigned subjects'),
            'attributes.subject_sections.in' => trans('general_quizzes.Invalid subject section')
        ];
    }

    protected function prepareForValidation()
    {
        $user = Auth::guard('api')->user();
        if ($this->route('homework') && $user = Auth::guard('api')->user()) {
            $homework = $this->route('homework');
            $instructorSubjects = $homework->creator->schoolInstructorSubjects()->pluck('subjects.id')->toArray() ?? [];
            $supervisorAssignedSubjects = $user->educationalSupervisorSubjects()->pluck('subjects.id')->toArray() ?? [];
            $this->subjects = array_intersect($supervisorAssignedSubjects, $instructorSubjects);
            $this->sections = SubjectFormatSubject::where('subject_id', $this->validationData()['subject_id'] ?? -1)->pluck('id')->toArray();

            // if (!is_null($homework->start_at) && new \Carbon\Carbon($homework->start_at) < now()) {
            //     $validator = $this->getValidatorInstance();
            //     $validator->after(
            //         function ($validator) {
            //             $validator->errors()->add('start_at', trans('general_quizzes.Can not update homework which already started'));
            //         }
            //     );
            // }
        }
    }
}
