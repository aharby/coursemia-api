<?php


namespace App\OurEdu\Quizzes\Requests\PeriodicTest;

use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;
use App\OurEdu\Quizzes\Quiz;
use Carbon\Carbon;

class UpdatePeriodicTestRequest extends BaseApiParserRequest
{
    public function rules()
    {
        $rules = [
            'attributes.end_at' => 'required|date|after:start_date',
            'attributes.grade_class_id' => 'required|integer|exists:grade_classes,id',
        ];
        if ($this->route('periodicTestId')) {
            $quiz = Quiz::whereId($this->route('periodicTestId'))->firstOrFail();
            if ((new Carbon($this->data['attributes']['start_at']))->format('Y-m-d H:i:s') != $quiz->start_at) {
                $rules['attributes.start_at'] = 'required|date|before:end_at|after:' . now()->addHour();
            }
        }
        return $rules;
    }

    protected function prepareForValidation()
    {
        if ($this->route('periodicTestId')) {
            $quiz = Quiz::whereId($this->route('periodicTestId'))->firstOrFail();
            if ((new Carbon($quiz->start_at))->subHour() < now()) {
                if (new Carbon($quiz->start_at) < now()) {
                    $validator = $this->getValidatorInstance();
                    $validator->after(
                        function ($validator) {
                            $validator->errors()->add('start_at', trans('validation.Can not update periodic test which already started'));
                        }
                    );
                }
                $validator = $this->getValidatorInstance();
                $validator->after(
                    function ($validator) {
                        $validator->errors()->add('start_at', trans('validation.Can not update periodic test which will start in less than one hour'));
                    }
                );
            }
        }
    }
}
