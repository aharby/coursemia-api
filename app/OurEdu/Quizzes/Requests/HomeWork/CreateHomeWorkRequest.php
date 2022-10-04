<?php


namespace App\OurEdu\Quizzes\Requests\HomeWork;

use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;
use App\OurEdu\Quizzes\Enums\QuizTypesEnum;
use App\OurEdu\Quizzes\Quiz;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class CreateHomeWorkRequest extends BaseApiParserRequest
{
    public function rules()
    {
        $data = request()->get('data')['attributes'];

        return [
            'attributes.start_at' => 'required|date|before:end_at|after:' . now()->addHour(),
            'attributes.end_at' => 'required|date|after:start_date',
            'attributes.classroom_class_session_id' => [
                'required',
                'integer',
                'exists:classroom_class_sessions,id',
                function ($attribute, $value, $fail) use ($data) {
                    $quiz = Quiz::query()
                        ->where('classroom_class_session_id', "=", $data['classroom_class_session_id'])
                        ->where("quiz_type", "=", QuizTypesEnum::HOMEWORK)
                        ->where(
                            function (Builder $query) use ($data) {
                                $query->whereBetween('start_at', [$data['start_at'], $data['end_at']])
                                ->orWhereBetween('end_at', [$data['start_at'], $data['end_at']])
                                ->orWhere(
                                    function (Builder $nestedQuery) use ($data) {
                                        $nestedQuery->where("start_at", "<=", $data['end_at'])
                                        ->where("end_at", ">=", $data['start_at']);
                                    }
                                );
                            }
                        )
                        ->first();
                    if ($quiz) {
                        $fail(
                            trans(
                                'quiz.there is a conflict with an existed quiz time',
                                [
                                "from" => Carbon::parse($quiz->start_at)->format("d/m h:i a"),
                                "to" => Carbon::parse($quiz->end_at)->format("d/m h:i a")
                                ]
                            )
                        );
                    }
                }
            ],
        ];
    }
}
