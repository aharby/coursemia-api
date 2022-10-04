<?php

namespace App\OurEdu\PsychologicalTests\Student\Requests\Api;

use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;
use App\OurEdu\PsychologicalTests\Models\PsychologicalTest;
use Illuminate\Validation\Rule;

class PsychologicalAnswerRequest extends BaseApiParserRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'attributes.option_id' => ['required', 'integer', Rule::in($this->options)],
            'attributes.question_id' => ['required', 'integer', Rule::in($this->questions)],
        ];
    }

    protected function prepareForValidation()
    {
        $test = PsychologicalTest::with('options', 'questions')->findOrFail($this->route('id'));

        $this->options = $test->options->where('is_active', true)->pluck('id');
        $this->questions = $test->questions->where('is_active', true)->pluck('id');
    }
}
