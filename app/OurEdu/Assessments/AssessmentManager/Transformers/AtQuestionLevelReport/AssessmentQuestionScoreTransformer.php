<?php

namespace App\OurEdu\Assessments\AssessmentManager\Transformers\AtQuestionLevelReport;

use App\OurEdu\Assessments\AssessmentManager\Transformers\AssessmentCategoryTransformer;
use App\OurEdu\Assessments\Enums\QuestionTypesEnums;
use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\Assessments\Models\AssessmentQuestion;
use App\OurEdu\Assessments\Transformers\QuestionsTransformers\EssayQuestion\EssayQuestionTransformer;
use App\OurEdu\Assessments\Transformers\QuestionsTransformers\Matrix\MatrixQuestionTransformer;
use App\OurEdu\Assessments\Transformers\QuestionsTransformers\MultipleChoice\MultipleChoiceQuestionTransformer;
use App\OurEdu\Assessments\Transformers\QuestionsTransformers\SatisfactionRating\SatisfactionRatingQuestionTransformer;
use App\OurEdu\Assessments\Transformers\QuestionsTransformers\StarRating\StarRatingQuestionTransformer;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;

class AssessmentQuestionScoreTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'questions'
    ];


    protected array $availableIncludes = [];
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
        ];

        if ($assessmentQuestion->slug != QuestionTypesEnums::ESSAY_QUESTION) {
            $score_percentage = $assessmentQuestion->question_grade > 0
                ? ($this->getScore($assessmentQuestion) / $assessmentQuestion->question_grade) * 100
                : 0;
            $data["score_percentage"] = (float)number_format($score_percentage, 2);
        }
        return $data;
    }

    private function getScore(AssessmentQuestion $assessmentQuestion): float
    {
        $score = $assessmentQuestion->average_score;
        if ($this->params["hasBranch"]) {
            $score = $assessmentQuestion->branchScores[0]->pivot->score ?? 00.0;
        }

        return $score;
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
                    new EssayQuestionTransformer(),
                    ResourceTypesEnums::QUESTION
                );
        }
    }

    public function includeCategory(AssessmentQuestion $assessmentQuestion)
    {
        if (!is_null($assessmentQuestion->category)) {
            return $this->item(
                $assessmentQuestion->category,
                new AssessmentCategoryTransformer(),
                ResourceTypesEnums::ASSESSMENT_CATEGORY
            );
        }
    }
}
