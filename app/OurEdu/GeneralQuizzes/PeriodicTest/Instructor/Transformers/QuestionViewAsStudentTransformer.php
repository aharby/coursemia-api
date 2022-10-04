<?php


namespace App\OurEdu\GeneralQuizzes\PeriodicTest\Instructor\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizQuestionBank;
use App\OurEdu\GeneralQuizzes\Transformers\QuestionsTransformers\QuestionTransformer;
use League\Fractal\TransformerAbstract;

class QuestionViewAsStudentTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'questionData',
        'actions'
    ];
    /**
     * @var GeneralQuiz
     */
    private $generalQuiz;
    private $params;
    private bool $added_from_bank_questions;

    /**
     * QuestionTransformer constructor.
     * @param GeneralQuiz $generalQuiz
     */
    public function __construct(GeneralQuiz $generalQuiz, $params = [])
    {
        $this->generalQuiz = $generalQuiz;
        $this->params = $params;
    }

    public function transform(GeneralQuizQuestionBank $questionBank)
    {
        $question = $questionBank->generalQuiz()
            ->where('general_quiz_id', $this->generalQuiz->id)->first();

        $this->added_from_bank_questions = $question && $question->pivot && $question->pivot->added_from_bank == 1;

        return [
            "id" => (int)$questionBank->id,
            'type' => (string)$questionBank->slug,
            'direction' => (string)$questionBank->subject->direction,
            'added_from_bank' => $this->added_from_bank_questions
        ];
    }

    public function includeQuestionData(GeneralQuizQuestionBank $questionBank)
    {
        return $this->item($questionBank->question, new QuestionTransformer($this->generalQuiz), ResourceTypesEnums::HOMEWORK_QUESTION_DATA);
    }

    public function includeActions(GeneralQuizQuestionBank $questionBank)
    {
        $actions = [];
        if(is_null($this->generalQuiz->published_at)){
            $actions[] = [
                'endpoint_url' => buildScopeRoute(
                    'api.general-quizzes.periodic-test.instructors.delete.question',
                    [
                        'periodicTest' => $this->generalQuiz->id,
                        'question' => $questionBank->id
                    ]
                ),
                'label' => trans('app.Delete'),
                'method' => 'DELETE',
                'key' => APIActionsEnums::DELETE_QUESTION
            ];
        }

        if (is_null($this->generalQuiz->published_at) && !$this->added_from_bank_questions) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute(
                    'api.general-quizzes.periodic-test.instructors.post.questions_store',
                    ['periodicTest' => $this->generalQuiz->id]
                ),
                'label' => trans('app.edit Periodic Test'),
                'method' => 'POST',
                'key' => APIActionsEnums::EDIT_PERIODIC_TEST_QUESTIONS
            ];
        }

        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }
}
