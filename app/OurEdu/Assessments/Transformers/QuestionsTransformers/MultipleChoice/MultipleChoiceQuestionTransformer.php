<?php

namespace App\OurEdu\Assessments\Transformers\QuestionsTransformers\MultipleChoice;

use App\OurEdu\Assessments\Models\AssessmentUser;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Assessments\Models\Questions\MultipleChoice\AssessmentMultipleChoiceQuestion;
use League\Fractal\TransformerAbstract;

class MultipleChoiceQuestionTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'options',
        'answer'
    ];
    /**
     * @var array
     */
    private $params;

    /**
     * MultipleChoiceQuestionTransformer constructor.
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->params = $params;
    }

    public function transform(AssessmentMultipleChoiceQuestion $question)
    {
        return [
            'id' => (int)$question->id,
            'question' => (string)$question->question,
            "url" => (string)$question->url
        ];
    }

    public function includeOptions(AssessmentMultipleChoiceQuestion $question)
    {
        $options = $question->options;

        return $this->collection(
            $options,
            new MultipleChoiceQuestionOptionsTransformer(),
            ResourceTypesEnums::QUESTION_OPTIONS
        );
    }

    public function includeAnswer(AssessmentMultipleChoiceQuestion $question)
    {
        $answerTransformer = null;
        $assessmentUser = $this->params['assessmentUser'] ?? null;

        if (
            isset($this->params['assessor'])
            and isset($this->params['assessee'])
            and isset($this->params['assessment'])
        ) {
            $assessor = $this->params['assessor'];
            $assessee = $this->params['assessee'];
            $assessment = $this->params['assessment'];

            $assessmentUser = AssessmentUser::query()
                ->where("assessment_id", "=", $assessment->id)
                ->where("user_id", "=", $assessor->id)
                ->where("assessee_id", "=", $assessee->id)
                ->where("is_finished", "=", false)
                ->orderByDesc("id")
                ->first();
        }

        if ($assessmentUser) {
            $assessmentQuestion = $question->assessmentQuestion()->first();

            $answer = $assessmentUser->answers()
                ->where('assessment_question_id', $assessmentQuestion->id)
                ->first();

            if ($answer) {
                $answerTransformer = $this->item(
                    $answer,
                    new MultipleChoiceQuestionAnswerTransformer($this->params),
                    ResourceTypesEnums::Assessment_QUESTION_ANSWERS
                );
            }
        }

        return $answerTransformer;
    }
}
