<?php

namespace App\OurEdu\GeneralQuizzes\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Transformers\QuestionsTransformers\QuestionTransformer;
use League\Fractal\TransformerAbstract;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizQuestionBank;

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
        return [
            "id" => (int)$questionBank->id,
            'question_type' => (string)$questionBank->slug,
            'direction' => (string)$questionBank->subject->direction,
            'grade' => $questionBank->grade,
            "section_id" => (int)$questionBank->subject_format_subject_id,
            "section_name" => (string)$questionBank->section->title ?? null,
        ];
    }

    public function includeQuestionData(GeneralQuizQuestionBank $questionBank)
    {
        $questions = $questionBank->question ?? $questionBank->questions;

        if ($questions) {
            return $this->item(
                $questions,
                new QuestionTransformer($this->generalQuiz),
                ResourceTypesEnums::HOMEWORK_QUESTION_DATA
            );
        }
    }

    public function includeActions(GeneralQuizQuestionBank $questionBank)
    {
        $actions[] = [
            'endpoint_url' => buildScopeRoute(
                'api.general-quizzes.homework.instructor.delete.question',
                [
                    'homework' => $this->generalQuiz->id,
                    'question' => $questionBank->id
                ]
            ),
            'label' => trans('app.Delete'),
            'method' => 'DELETE',
            'key' => APIActionsEnums::DELETE_QUESTION
        ];

        $actions[] = [
            'endpoint_url' => buildScopeRoute(
                'api.general-quizzes.homework.instructor.post.questions_store',
                ['homework' => $this->generalQuiz->id]
            ),
            'label' => trans('app.Edit HomeWork questions'),
            'method' => 'POST',
            'key' => APIActionsEnums::EDIT_HOMEWORK_QUESTIONS
        ];

        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }
}
