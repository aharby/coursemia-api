<?php

namespace App\OurEdu\Assessments\Transformers\QuestionsTransformers\EssayQuestion;

use App\OurEdu\Assessments\Models\AssessmentUser;
use App\OurEdu\Assessments\Models\Questions\Essay\AssessmentEssayQuestion;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use League\Fractal\TransformerAbstract;

class EssayQuestionTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
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

    public function transform(AssessmentEssayQuestion $question)
    {
        return [
            'id' => (int)$question->id,
            'question' => (string)$question->question
        ];
    }

    public function includeAnswer(AssessmentEssayQuestion $question)
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
                    new EssayQuestionAnswerTransformer(),
                    ResourceTypesEnums::Assessment_QUESTION_ANSWERS
                );
            }
        }

        if (isset($this->params['essay_answers'])) {
            $answers = $question->assessmentQuestion
                ->assessorsAnswers()
                ->get();
            $answerTransformer = $this->collection(
                $answers,
                new EssayQuestionAnswerTransformer(),
                ResourceTypesEnums::Assessment_QUESTION_ANSWERS
            );
        }

        return $answerTransformer;
    }
}
