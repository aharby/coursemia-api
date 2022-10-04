<?php

namespace App\OurEdu\GeneralQuizzes\Transformers\QuestionsTransformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralQuizzes\Enums\QuestionsTypesEnums;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropData;
use App\OurEdu\ResourceSubjectFormats\Models\QuestionHeadInterface;
use App\OurEdu\Users\User;
use League\Fractal\TransformerAbstract;

class QuestionTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'questions',
    ];
    protected array $availableIncludes = [
        'actions'
    ];
    /**
     * @var GeneralQuiz
     */
    private $generalQuiz;
    private $student;
    private $slug;
    private $questionBank;
    /**
     * @var array|null[]
     */
    private $params;

    /**
     * QuestionTransformer constructor.
     * @param GeneralQuiz $generalQuiz
     * @param User|null $student
     * @param array $params
     */
    public function __construct(GeneralQuiz $generalQuiz, User $student = null, array $params = [null])
    {
        $this->generalQuiz = $generalQuiz;
        $this->student = $student;
        $this->params = $params;
    }

    public function transform(QuestionHeadInterface $questionData)
    {
        $questionData = $questionData->questionHead();

        if ($questionData instanceof DragDropData) {
            $this->slug = $questionData->questionBank->slug;
            $this->questionBank = $questionData->questionBank;
        } else {
            $this->slug = $questionData->questions->first()->questionBank->slug;
            $this->questionBank = $questionData->questions()->first()->questionBank()->first();
        }

        return [
            "id" => (int)$questionData->id,
            'description' => (string)$questionData->description,
            'question_slug' => (string)$this->slug,
            'type' => (string)QuestionsTypesEnums::getLabel($this->slug),
            "question_bank" => (int)$this->questionBank->id ?? null,
        ];
    }


    public function includeQuestions(QuestionHeadInterface $questionData)
    {
        $questionData = $questionData->questionHead();
        $questions = $questionData->questions;

        $params = [];
        if (isset($this->params["show_if_is_correct"])) {
            $params["show_if_is_correct"] = $this->params["show_if_is_correct"];
        }
        if (isset($this->params["course_homework"])) {
            $params["course_homework"] = $this->params["course_homework"];
        }

        $params['generalQuiz'] = $this->generalQuiz;


        if ($this->student) {
            $params['student'] = $this->student;
        }
        switch ($this->slug) {
            case QuestionsTypesEnums::SINGLE_CHOICE:
            case QuestionsTypesEnums::MULTI_CHOICE:
                return $this->collection(
                    $questions,
                    new MultipleChoiceQuestionTransformer($params),
                    ResourceTypesEnums::QUESTION
                );
            case QuestionsTypesEnums::TRUE_FALSE:
                return $this->collection(
                    $questionData->questions,
                    new TrueFalseQuestionTransformer($params),
                    ResourceTypesEnums::QUESTION
                );
            case QuestionsTypesEnums::TRUE_FALSE_WITH_CORRECT:
                return $this->collection(
                    $questions,
                    new TrueFalseQuestionWithChoiceTransformer($params),
                    ResourceTypesEnums::QUESTION
                );
            case QuestionsTypesEnums::ESSAY:
                return $this->collection(
                    $questions,
                    new EssayQuestionTransformer($params),
                    ResourceTypesEnums::QUESTION
                );
            case QuestionsTypesEnums::DRAG_DROP_TEXT:
            case QuestionsTypesEnums::DRAG_DROP_IMAGE:
                if ($this->questionBank) {
                    $params['questionBank'] = $this->questionBank;
                }
                return $this->item(
                    $questionData,
                    new DragDropQuestionTransformer($params),
                    ResourceTypesEnums::HOMEWORK_QUESTION_DATA
                );
            case QuestionsTypesEnums::COMPLETE:
                return $this->collection(
                    $questions,
                    new CompleteQuestionTransformer($params),
                    ResourceTypesEnums::QUESTION
                );
        }
    }

    public function includeActions(QuestionHeadInterface $questionData)
    {
        $actions[] = [
            'endpoint_url' => buildScopeRoute(
                'api.general-quizzes.homework.instructor.view.question',
                ["homework" => $this->generalQuiz, 'questionBank' => $this->questionBank->id ?? null]
            ),
            'label' => trans('general_quizzes.view question'),
            'method' => 'GET',
            'key' => APIActionsEnums::VIEW_HOMEWORK_QUESTION
        ];

        if (!$this->generalQuiz->published_at) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute(
                    'api.general-quizzes.homework.instructor.post.questions_store',
                    ['homework' => $this->generalQuiz]
                ),
                'label' => trans('general_quizzes.edit question'),
                'method' => 'POST',
                'key' => APIActionsEnums::EDIT_HOMEWORK_QUESTIONS
            ];
        }

        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }
}
