<?php


namespace App\OurEdu\GeneralQuizzes\Homework\Student\Transformers;


use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudent;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;
use App\OurEdu\GeneralQuizzes\Transformers\QuestionsTransformers\QuestionTransformer;
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
        $homework = $generalQuizStudent->generalQuiz()->firstOrFail();
        $student = $generalQuizStudent->user()->firstOrFail();
        $params["show_if_is_correct"] = true;

        $bankQuestions = $homework->questions()->get();

        $questions = [];

        foreach ($bankQuestions as $question) {
            if (isset($question->questions)) {
                $questions[] = $question->questions;
            }
        }

        return $this->collection($questions, new QuestionTransformer($homework, $student, $params), ResourceTypesEnums::HOMEWORK_QUESTION_DATA);

    }
}
