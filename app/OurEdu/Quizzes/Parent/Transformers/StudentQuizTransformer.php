<?php

namespace App\OurEdu\Quizzes\Parent\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Quizzes\Enums\QuizTypesEnum;
use App\OurEdu\Quizzes\Models\AllQuizStudent;
use App\OurEdu\Quizzes\Parent\QuizzesPerformance;
use App\OurEdu\Quizzes\Quiz;
use Carbon\Carbon;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class StudentQuizTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];
    protected array $availableIncludes = [
    ];

    protected $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform(AllQuizStudent $quiz)
    {
        $transformedData = [
            'id' =>  Str::uuid(),
            'start_date' => Carbon::parse($quiz->quiz->start_at)->format("Y/m/d") ,
            'start_time' => Carbon::parse($quiz->quiz->start_at)->format("H:i") ,
            'quiz_id' => $quiz->quiz_id ,
            'quiz_result_percentage' =>  $quiz->quiz_result_percentage,
            'quiz_type' => $quiz->quiz_type,
            'subject' => $quiz->quiz->subject->name ?? "",
            'is_attend' => isset($quiz->taken_at),
            'time' => Carbon::parse($quiz->quiz->start_at)->diffInMinutes(Carbon::parse($quiz->quiz->end_at)),
            "student_time" => $this->calculateStudentTime($quiz),
        ];
        return $transformedData;
    }

    private function calculateStudentTime(AllQuizStudent $quiz)
    {
        $startedAt = $quiz->quiz->studentQuiz[0]->started_at ?? null ;
        $finishedAt = $quiz->quiz->studentQuiz[0]->finished_at ?? null ;

        if ($startedAt and $finishedAt) {
            return Carbon::parse($startedAt)->diffInMinutes(Carbon::parse($finishedAt));
        }

        return 0;
    }
}
