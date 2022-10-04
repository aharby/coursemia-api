<?php

namespace App\OurEdu\GeneralExams\SME\Requests;

use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;
use App\OurEdu\GeneralExams\GeneralExam;
use App\OurEdu\GeneralExams\Models\PreparedGeneralExamQuestion;
use Illuminate\Validation\Rule;

class GeneralExamQuestionsRequest extends BaseApiParserRequest
{
    protected $availableQuestions = [];

    public function rules()
    {
        return [
            'attributes.prepared_questions' => ['required', 'array', Rule::in($this->availableQuestions)],
        ];
    }

    protected function prepareForValidation()
    {
        $exam = GeneralExam::findOrFail($this->route('exam'));

        $this->availableQuestions = PreparedGeneralExamQuestion::where('subject_id', $exam->subject_id)->pluck('id')->toArray();
    }
}
