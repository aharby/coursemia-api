<?php

namespace App\OurEdu\Assessments\AssessmentManager\Transformers;

use App\OurEdu\Assessments\Transformers\QuestionsTransformers\EssayQuestion\EssayQuestionTransformer;
use App\OurEdu\Assessments\Transformers\QuestionsTransformers\SatisfactionRating\SatisfactionRatingQuestionTransformer;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\Assessments\Models\AssessmentQuestion;
use League\Fractal\TransformerAbstract;
use App\OurEdu\Assessments\Enums\QuestionTypesEnums;
use App\OurEdu\Assessments\Transformers\QuestionsTransformers\MultipleChoice\MultipleChoiceQuestionTransformer;
use App\OurEdu\Assessments\Transformers\QuestionsTransformers\StarRating\StarRatingQuestionTransformer;
use App\OurEdu\Assessments\Transformers\QuestionsTransformers\Matrix\MatrixQuestionTransformer;

class AssessmentQuestionTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'questions'
    ];

    protected array $availableIncludes = ['actions'];
    /**
     * @var Assessment
     */
    protected $assessment;
    protected $params;

    /**
     * QuestionTransformer constructor.
     * @param Assessment $assessment
     * @param array $params
     */
    public function __construct(Assessment $assessment, $params = [])
    {
        $this->assessment = $assessment;
        $this->params = $params;
    }

    public function transform(AssessmentQuestion $assessmentQuestion)
    {
        $data = [
            "id" => (int)$assessmentQuestion->id,
            'question_type' => (string)$assessmentQuestion->slug,
            'skip_question' => (bool)$assessmentQuestion->skip_question,
            'category' => $assessmentQuestion->category->title ?? null,
            'category_id' => $assessmentQuestion->category_id,
            'answers_count' => $assessmentQuestion->assessors_answers_count
        ];

        if ($assessmentQuestion->slug != QuestionTypesEnums::ESSAY_QUESTION) {
            $data['question_grade'] = (float)$assessmentQuestion->question_grade;
        }

        return $data;
    }

    public function includeQuestions(AssessmentQuestion $assessmentQuestion)
    {
        $question = $assessmentQuestion->question;
        $params = [];
        $params['assessment'] = $this->assessment;

        switch ($assessmentQuestion->slug) {
            case QuestionTypesEnums::SINGLE_CHOICE:
            case QuestionTypesEnums::MULTI_CHOICE:
                return $this->item(
                    $question,
                    new MultipleChoiceQuestionTransformer($params),
                    ResourceTypesEnums::QUESTION
                );
            case QuestionTypesEnums::SCALE_RATING:
            case QuestionTypesEnums::STAR_RATING:
                return $this->item(
                    $question,
                    new StarRatingQuestionTransformer($params),
                    ResourceTypesEnums::QUESTION
                );
            case QuestionTypesEnums::MATRIX:
                return $this->item(
                    $question,
                    new MatrixQuestionTransformer($params),
                    ResourceTypesEnums::QUESTION
                );
            case QuestionTypesEnums::SATISFICATION_RATING:
                return $this->item(
                    $question,
                    new SatisfactionRatingQuestionTransformer($params),
                    ResourceTypesEnums::QUESTION
                );
            case QuestionTypesEnums::ESSAY_QUESTION:
                return $this->item(
                    $question,
                    new EssayQuestionTransformer($this->params),
                    ResourceTypesEnums::QUESTION
                );
        }
    }


    public function includeActions(AssessmentQuestion $assessmentQuestion)
    {
        $actions[] = [
            'endpoint_url' => buildScopeRoute(
                'api.assessments.assessment-manager.delete.question',
                [
                    'assessment' => $this->assessment->id,
                    'assessmentQuestion' => $assessmentQuestion->id
                ]
            ),
            'label' => trans('app.Delete'),
            'method' => 'DELETE',
            'key' => APIActionsEnums::DELETE_QUESTION
        ];
        if (!isset($this->params['viewQuestion'])) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute(
                    'api.assessments.assessment-manager.view.question',
                    ["assessment" => $this->assessment, 'assessmentQuestion' => $assessmentQuestion->id ?? null]
                ),
                'label' => trans('app.view'),
                'method' => 'GET',
                'key' => APIActionsEnums::VIEW_ASSESSMENT_QUESTION
            ];
        }

        $actions[] = [
            'endpoint_url' => buildScopeRoute(
                'api.assessments.assessment-manager.post.questions_store',
                ['assessment' => $this->assessment]
            ),
            'label' => trans('app.Save'),
            'method' => 'POST',
            'key' => APIActionsEnums::EDIT_ASSESSMENT_QUESTIONS
        ];

        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.assessments.assessment-manager.post.cloneQuestion', [
                'assessment' => $this->assessment,
                'assessmentQuestion' => $assessmentQuestion
            ]),
            'label' => trans('app.Save'),
            'method' => 'POST',
            'key' => APIActionsEnums::CLONE_ASSESSMENT_QUESTIONS
        ];

        return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
    }
}
