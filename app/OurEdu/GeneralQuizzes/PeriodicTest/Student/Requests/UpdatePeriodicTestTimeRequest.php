<?php


namespace App\OurEdu\GeneralQuizzes\PeriodicTest\Student\Requests;

use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudent;
use Illuminate\Support\Facades\Auth;

class UpdatePeriodicTestTimeRequest extends BaseApiParserRequest
{
    public function rules()
    {
        if ($this->student_test_duration_limit > 0){
            return [
                'attributes.student_test_duration' => 'required|integer|lte:' . $this->student_test_duration_limit
            ];
        }
        return [
            'attributes.student_test_duration' => 'required|integer'
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->route('periodicTest') && $user = Auth::guard('api')->user()) {
            $studentGeneralQuiz = GeneralQuizStudent::where('general_quiz_id', $this->route('periodicTest')->id)->where('student_id', auth()->id())->first();
            if($studentGeneralQuiz){
                $this->student_test_duration_limit = $studentGeneralQuiz->student_test_duration;
            }
        }
    }
}
