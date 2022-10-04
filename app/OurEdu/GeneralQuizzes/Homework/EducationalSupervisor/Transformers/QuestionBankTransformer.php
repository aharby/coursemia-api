<?php


namespace App\OurEdu\GeneralQuizzes\Homework\EducationalSupervisor\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralQuizzes\Enums\QuestionsPublicStatusesEnums;
use App\OurEdu\GeneralQuizzes\Enums\QuestionsTypesEnums;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Transformers\QuestionsTransformers\QuestionTransformer;
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
    ];

    protected array $availableIncludes = ['actions'];
    /**
     * @var GeneralQuiz
     */
    private $generalQuiz;
    private $params;
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
        $this->added_from_bank_questions =
            $question && $question->pivot &&  $question->pivot->added_from_bank == 1 ? true:false;
        return [
            "id" => (int)$questionBank->id,
            'question_type' => (string)$questionBank->slug ,
            'direction'=>(string)$questionBank->subject->direction,
            'grade' => $questionBank->grade,
            "section_id" => (int)$questionBank->subject_format_subject_id,
            "section_name" => (string)$questionBank->section->title ?? null,
            "added_from_bank_questions"=>(bool)$this->added_from_bank_questions,
            "public_status"=>(bool) ($questionBank->public_status != QuestionsPublicStatusesEnums::PRIVATE),
        ];
    }

    public function includeQuestionData(GeneralQuizQuestionBank $questionBank)
    {
        return $this->item($questionBank->question ?? $questionBank->questions, new QuestionTransformer($this->generalQuiz), ResourceTypesEnums::HOMEWORK_QUESTION_DATA);
    }

    public function includeActions(GeneralQuizQuestionBank $questionBank)
    {
        $actions[] = [
            'endpoint_url' => buildScopeRoute(
                'api.general-quizzes.homework.educational-supervisor.delete.question',
                ['homework' => $this->generalQuiz->id,
                'question' => $questionBank->id]
            ),
            'label' => trans('app.Delete'),
            'method' => 'DELETE',
            'key' => APIActionsEnums::DELETE_QUESTION
        ];
        if (!isset($this->params['viewQuestion'])) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.general-quizzes.homework.educational-supervisor.view.question', ["homework" => $this->generalQuiz, 'questionBank' => $questionBank->id ?? null]),
                'label' => trans('general_quizzes.view question'),
                'method' => 'GET',
                'key' => APIActionsEnums::VIEW_HOMEWORK_QUESTION
            ];
        }

        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.general-quizzes.homework.educational-supervisor.post.questions_store', ['homework' => $this->generalQuiz]),
            'label' => trans('general_quizzes.edit question'),
            'method' => 'POST',
            'key' => APIActionsEnums::EDIT_HOMEWORK_QUESTIONS
        ];

        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }
}
