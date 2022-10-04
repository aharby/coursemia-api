<?php


namespace App\OurEdu\Quizzes\Requests\HomeWork;

use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;
use App\OurEdu\Quizzes\Quiz;
use Carbon\Carbon;

class UpdateHomeWorkRequest extends BaseApiParserRequest
{
    public function rules()
    {
        $rules = [
            'attributes.end_at' => 'required|date|after:start_date',
            'attributes.classroom_class_session_id' => 'required|integer|exists:classroom_class_sessions,id',
        ];
        if ($this->route('homeworkId')) {
            $quiz = Quiz::whereId($this->route('homeworkId'))->firstOrFail();
            if ((new Carbon($this->data['attributes']['start_at']))->format('Y-m-d H:i:s') != $quiz->start_at) {
                $rules['attributes.start_at'] = 'required|date|before:end_at|after:' . now()->addHour();
            }
        }
        return $rules;
    }

    protected function prepareForValidation()
    {
        if ($this->route('homeworkId')) {
            $quiz = Quiz::whereId($this->route('homeworkId'))->firstOrFail();
            if ((new Carbon($quiz->start_at))->subHour() < now()) {
                if (new Carbon($quiz->start_at) < now()) {
                    $validator = $this->getValidatorInstance();
                    $validator->after(
                        function ($validator) {
                            $validator->errors()->add('start_at', trans('validation.Can not update homework which already started'));
                        }
                    );
                }
                $validator = $this->getValidatorInstance();
                $validator->after(
                    function ($validator) {
                        $validator->errors()->add('start_at', trans('validation.Can not update homework which will start in less than one hour'));
                    }
                );
            }
        }
    }
}
