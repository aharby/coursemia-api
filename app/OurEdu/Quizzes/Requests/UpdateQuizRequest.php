<?php


namespace App\OurEdu\Quizzes\Requests;

use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;
use App\OurEdu\Quizzes\Enums\QuizTimesEnum;
use Illuminate\Validation\Rule;

class UpdateQuizRequest extends BaseApiParserRequest
{
    public function rules()
    {
        return [

            'attributes.classroom_class_session_id' => 'required|integer|exists:classroom_class_sessions,id',
            'attributes.quiz_time' => ['required', Rule::in(QuizTimesEnum::getQuizTimes())]
        ];
    }
}
