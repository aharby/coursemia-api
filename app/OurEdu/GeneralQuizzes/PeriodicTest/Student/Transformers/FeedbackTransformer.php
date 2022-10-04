<?php


namespace App\OurEdu\GeneralQuizzes\PeriodicTest\Student\Transformers;


use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudent;
use League\Fractal\TransformerAbstract;

class FeedbackTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'questions'
    ];

    protected array $availableIncludes = [
    ];

    public function transform(GeneralQuizStudent $generalQuizStudent)
    {
        $totalScore = $generalQuizStudent->generalQuiz->mark ?? 0;

        return [
            "id" => (int)$generalQuizStudent->id,
            "start_at" => (string)$generalQuizStudent->start_at,
            "finished_at" => (string)$generalQuizStudent->finish_at,
            "score_percentage" => (float)$generalQuizStudent->score_percentage,
            "score" => (float)$generalQuizStudent->score,
            "total_score" => (float)$totalScore,
        ];
    }

    public function includeQuestions(GeneralQuizStudent $generalQuizStudent)
    {
        $periodicTest = $generalQuizStudent->generalQuiz()->firstOrFail();
        $params["show_if_is_correct"] = true;

        $bankQuestions = $periodicTest->questions()->get();

        $questions = [];

        foreach ($bankQuestions as $question) {
            if (isset($question->questions)) {
                $questions[] = $question->questions;
            }
        }

        return $this->collection($questions, new QuestionTransformer($params), ResourceTypesEnums::Periodic_Test_QUESTION_DATA);
    }
}
