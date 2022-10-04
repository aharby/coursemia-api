<?php

namespace App\OurEdu\Exams\Student\Requests\Practices;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\OurEdu\BaseApp\Requests\BaseAppRequest;
use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;

class GeneratePracticeRequest extends BaseApiParserRequest
{
    protected $subjects = [];
    protected $sections = [];

    public function rules()
    {
        return [
            'attributes.subject_id' => ['required', Rule::in($this->subjects)],
            'attributes.subject_format_subject_ids'=> ['required', 'array', Rule::in($this->sections)],
        ];
    }

    protected function prepareForValidation()
    {
        if ($user = Auth::guard('api')->user()) {
            $this->subjects = $user->student->subjects()->pluck('subjects.id')->toArray() ?? [];

            $this->sections = SubjectFormatSubject::where('subject_id', $this->validationData()['attributes']['subject_id'] ?? -1)->pluck('id')->toArray();
        }
    }
}
