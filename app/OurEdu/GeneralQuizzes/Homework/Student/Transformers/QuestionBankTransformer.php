<?php


namespace App\OurEdu\GeneralQuizzes\Homework\Student\Transformers;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralQuizzes\Enums\QuestionsTypesEnums;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\ResourceSubjectFormats\Models\QuestionHeadInterface;
use League\Fractal\TransformerAbstract;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizQuestionBank;
use App\OurEdu\GeneralQuizzes\Transformers\QuestionsTransformers\MultipleChoiceQuestionTransformer;
use App\OurEdu\GeneralQuizzes\Transformers\QuestionsTransformers\TrueFalseQuestionTransformer;
use App\OurEdu\GeneralQuizzes\Transformers\QuestionsTransformers\TrueFalseQuestionWithChoiceTransformer;
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
    private $params;
    /**
     * QuestionTransformer constructor.
     * @param GeneralQuiz $generalQuiz
     */
    public function __construct(GeneralQuiz $generalQuiz,$params=[])
    {
        $this->generalQuiz = $generalQuiz;
        $this->params = $params;
    }

    public function transform(GeneralQuizQuestionBank $questionBank)
    {
        return [
            "id" => (int)$questionBank->id,
            'type' => (string)$questionBank->slug ,
            'direction'=>(string)$questionBank->subject?->direction
        ];
    }

    public function includeQuestionData (GeneralQuizQuestionBank $questionBank) {
        $this->params['generalQuiz'] = $this->generalQuiz;
        return $this->item($questionBank->question, new QuestionTransformer($this->params), ResourceTypesEnums::HOMEWORK_QUESTION_DATA);
    }

    public function includeActions(GeneralQuizQuestionBank $questionBank)
    {

        if (isset($this->params['finish_general_quiz'])) {
            $actions[] = [

                'endpoint_url' => buildScopeRoute('api.general-quizzes.homework.student.post.finish', ['homeworkId' => $this->generalQuiz->id]),
                'label' => trans('exam.Finish exam'),
                'method' => 'POST',
                'key' => APIActionsEnums::FINISH_HOMEWORK
            ];
        }

        $page = request()->input('page') ?? 1;

        $actions[] = [
            'endpoint_url' => buildScopeRoute(
                'api.general-quizzes.homework.student.post.answer',
                ['homeworkId' => $this->generalQuiz->id, 'page' => $page]
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
