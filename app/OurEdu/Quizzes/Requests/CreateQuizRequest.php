<?php


namespace App\OurEdu\Quizzes\Requests;

use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;
use App\OurEdu\Quizzes\Enums\QuizTimesEnum;
use App\OurEdu\Quizzes\Enums\QuizTypesEnum;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Lang;

class CreateQuizRequest extends BaseApiParserRequest
{
    public function rules()
    {
        $data = request()->get('data')['attributes'];
        $rules = [
            'attributes.classroom_class_session_id' => Rule::exists('classroom_class_sessions', 'id')
                ->where(
                    function ($query) {
                        return $query->where('from', '>', Carbon::now()->addHours(1)->toDateTimeString());
                    }
                ),
            'attributes.quiz_type' => [
                'required',
                Rule::in([QuizTypesEnum::QUIZ]),
            ],
            'attributes.quiz_time' => [
                'required',
                Rule::in(QuizTimesEnum::getQuizTimes()),
                Rule::unique('quizzes')->where(
                    function ($query) use ($data) {
                        return $query->where('classroom_class_session_id', $data['classroom_class_session_id'])
                            ->where('quiz_time', $data['quiz_time']);
                    }
                ),
            ],
        ];
        return $rules;
    }


    public function messages()
    {
        if (Lang::locale() == 'en') {
            return [
                'attributes.classroom_class_session_id.exists' => 'classroom class session id is invalid or it\'s starting time either passed or in less than 3 hours',
            ];
        }

        if (Lang::locale() == 'ar') {
            return [
                'attributes.classroom_class_session_id.exists' => 'هناك خطأ في رقم الحصة او انه قد مر وقت البدء أو في أقل من 3 ساعة على البدأ',
            ];
        }
    }
}
