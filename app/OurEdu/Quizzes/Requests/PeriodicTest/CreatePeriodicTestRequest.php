<?php


namespace App\OurEdu\Quizzes\Requests\PeriodicTest;

use App\OurEdu\BaseApp\Api\Requests\BaseApiParserRequest;
use App\OurEdu\Quizzes\Enums\QuizTypesEnum;
use App\OurEdu\Quizzes\Quiz;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;

class CreatePeriodicTestRequest extends BaseApiParserRequest
{
    public function rules()
    {
        $data = request()->get('data')['attributes'];
        return [
            'attributes.start_at' => 'required|date|before:end_at|after:' . now()->addHour(),
            'attributes.end_at' => 'required|date|after:start_date',
            'attributes.quiz_type' => [
                'required',
                Rule::in([QuizTypesEnum::PERIODIC_TEST]),
            ],
            'attributes.grade_class_id' => [
                'required',
                'integer',
                'exists:grade_classes,id',
                function ($attribute, $value, $fail) use ($data) {
                    $quiz = Quiz::query()
                        ->where("branch_id", "=", auth()->user()->branch_id)
                        ->where('grade_class_id', "=", $data['grade_class_id'])
                        ->where("quiz_type", "=", QuizTypesEnum::PERIODIC_TEST)
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
                                'quiz.there is a conflict with an existed periodicTest time',
                                [
                                    "from" => Carbon::parse($quiz->start_at)->format("d/m h:i a"),
                                    "to" => Carbon::parse($quiz->end_at)->format("d/m h:i a"),
                                    "instructor" => $quiz->creator->name ?? "",
                                    "grade_class" => $quiz->gradeClass->title ?? "",
                                    "subject" => $quiz->subject->name ?? "",
                                ]
                            )
                        );
                    }
                }
            ],
            'attributes.subject_id' => 'required|integer|exists:subjects,id'
        ];
    }
}
