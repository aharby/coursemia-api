<?php

namespace App\OurEdu\Assessments\Transformers\QuestionsTransformers\StarRating;

use App\OurEdu\Assessments\Models\AssessmentUser;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Assessments\Models\Questions\Rating\AssissmentRatingQuestion;
use League\Fractal\TransformerAbstract;

class StarRatingQuestionTransformer extends TransformerAbstract
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

    public function transform(AssissmentRatingQuestion $question)
    {
        return [
            'id' => (int)$question->id,
            'question' => (string)$question->question,
            "url" => (string)$question->url,
            'direction' => (string)$question->direction,
            "star_number" => $question->options->count()
        ];
    }

    public function includeOptions(AssissmentRatingQuestion $question)
    {
        $options = $question->options;

        return $this->collection(
            $options,
            new StarRatingQuestionOptionsTransformer(),
            ResourceTypesEnums::QUESTION_OPTIONS
        );
    }

    public function includeAnswer(AssissmentRatingQuestion $question)
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
                    new StarRatingQuestionAnswerTransformer($this->params),
                    ResourceTypesEnums::Assessment_QUESTION_ANSWERS
                );
            }
        }

        return $answerTransformer;
    }
}
