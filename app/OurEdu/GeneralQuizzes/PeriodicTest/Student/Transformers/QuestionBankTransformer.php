<?php


namespace App\OurEdu\GeneralQuizzes\PeriodicTest\Student\Transformers;


use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizQuestionBank;
use League\Fractal\TransformerAbstract;

class QuestionBankTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'questionData',
        'actions'
    ];
    /**
     * @var GeneralQuiz
     */
    private $generalQuiz;
    /**
     * @var array
     */
    private $params;

    /**
     * QuestionTransformer constructor.
     * @param GeneralQuiz $generalQuiz
     * @param array $params
     */
    public function __construct(GeneralQuiz $generalQuiz,array $params=[])
    {
        $this->generalQuiz = $generalQuiz;
        $this->params = $params;
    }

    public function transform(GeneralQuizQuestionBank $questionBank)
    {
        return [
            "id" => (int)$questionBank->id,
            'type' => (string)$questionBank->slug ,
            'direction'=> $questionBank->subject->direction ?? ""
        ];
    }

    public function includeQuestionData (GeneralQuizQuestionBank $questionBank)
    {
        $this->params['generalQuiz'] = $this->generalQuiz;
        return $this->item($questionBank->question, new QuestionTransformer($this->params), ResourceTypesEnums::HOMEWORK_QUESTION_DATA);
    }

    public function includeActions(GeneralQuizQuestionBank $questionBank)
    {

        if (isset($this->params['finish_general_quiz'])) {
            $actions[] = [

                'endpoint_url' => buildScopeRoute('api.general-quizzes.periodic-test.students.post.finish', ['periodicTest' => $this->generalQuiz->id]),
                'label' => trans('exam.Finish exam'),
                'method' => 'POST',
                'key' => APIActionsEnums::FINISH_PERIODIC_TEST
            ];
        }

        $page = request()->input('page') ?? 1;

        $actions[] = [
            'endpoint_url' => buildScopeRoute(
                'api.general-quizzes.periodic-test.students.post.answer',
                ['periodicTest' => $this->generalQuiz->id, 'page' => $page]
            ),
            'label' => trans('exam.Post answer'),
            'method' => 'POST',
            'key' => APIActionsEnums::POST_ANSWER
        ];

        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }

    }
}
