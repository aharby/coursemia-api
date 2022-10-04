<?php


namespace App\OurEdu\GeneralQuizzes\PeriodicTest\Instructor\Requests;

use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdatePeriodicTestRequest extends BaseApiParserRequest
{
    protected $subjects = [];
    protected $sections = [];

    public function rules()
    {

        $rules = [
            'attributes.end_at' => 'required|date_format:"Y-m-d H:i:s|after:attributes.start_at',
            'attributes.test_time' => 'required|integer|lte:' . $this->test_time_limit,
            'attributes.subject_id' => [
                'required', Rule::in($this->subjects)
            ],
            'attributes.grade_class_id' => 'required|integer|exists:grade_classes,id',
            'attributes.subject_sections' => [
                'required', 'array', Rule::in($this->sections)
            ],
            'attributes.random_question' => 'required|boolean',
            'attributes.title' => 'required|string',
        ];
        if ($this->route('periodicTestId')) {
            $periodicTest = GeneralQuiz::whereId($this->route('periodicTestId'))->firstOrFail();
            if ((new Carbon($this->data['attributes']['start_at']))->format('Y-m-d H:i:s') != $periodicTest->start_at) {
                $rules['attributes.start_at'] = 'required|date|before:attributes.end_at|after:' . now();
            }
        }
        return $rules;
    }

    public function messages()
    {
        return [
            'attributes.subject_id.in' => trans('general_quizzes.Invalid subject,please select from instructor\'s assigned subjects for the given grade'),
            'attributes.subject_sections.in' => trans('general_quizzes.Invalid subject section'),
            'attributes.test_time.lte' => trans('general_quizzes.test time must be less than test time limits'),
        ];
    }

    protected function prepareForValidation()
    {
        if ($user = Auth::guard('api')->user()) {
            $data = request()->get('data')['attributes'];
            $this->test_time_limit = Carbon::parse($data['start_at'])->diffInMinutes(Carbon::parse($data['end_at']));
            $this->subjects = $user->schoolInstructorSubjects()
                    ->where('grade_class_id', $data['grade_class_id'])
                    ->pluck('subjects.id')->toArray() ?? [];
            $this->sections = SubjectFormatSubject::where('subject_id', $data['subject_id'] ?? -1)->pluck('id')->toArray();
        }
    }
}
