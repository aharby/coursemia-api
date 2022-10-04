<?php


namespace App\OurEdu\Assessments\AssessmentManager\Transformers;

use App\OurEdu\Assessments\Enums\QuestionTypesEnums;
use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\Assessments\Models\AssessmentQuestion;
use App\OurEdu\Assessments\Transformers\QuestionsTransformers\EssayQuestion\EssayQuestionTransformer;
use App\OurEdu\Assessments\Transformers\QuestionsTransformers\Matrix\MatrixQuestionTransformer;
use App\OurEdu\Assessments\Transformers\QuestionsTransformers\MultipleChoice\MultipleChoiceQuestionTransformer;
use App\OurEdu\Assessments\Transformers\QuestionsTransformers\SatisfactionRating\SatisfactionRatingQuestionTransformer;
use App\OurEdu\Assessments\Transformers\QuestionsTransformers\StarRating\StarRatingQuestionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use League\Fractal\TransformerAbstract;

class QuestionViewAsAssessorTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'questions'
    ];

    private $assessment;
    private $params;

    /**
     * QuestionTransformer constructor.
     * @param Assessment $assessment
     */
    public function __construct(Assessment $assessment, $params = [])
    {
        $this->assessment = $assessment;
        $this->params = $params;
    }

    public function transform(AssessmentQuestion $assessmentQuestion)
    {
        return [
            'id' => (int)$assessmentQuestion->id,
            'question_type' => (string)$assessmentQuestion->slug,
            'introduction' => $this->assessment->introduction
        ];
    }

    public function includeQuestions(AssessmentQuestion $assessmentQuestion)
    {
        $question = $assessmentQuestion->question;
        $params = $this->params;
        $params['assessment'] = $this->assessment;

        switch ($assessmentQuestion->slug) {
            case QuestionTypesEnums::MULTI_CHOICE:
                return $this->item($question, new MultipleChoiceQuestionTransformer($params), ResourceTypesEnums::QUESTION);
                break;
            case QuestionTypesEnums::SINGLE_CHOICE:
                return $this->item($question, new MultipleChoiceQuestionTransformer($params), ResourceTypesEnums::QUESTION);
                break;
            case QuestionTypesEnums::STAR_RATING:
                return $this->item($question, new StarRatingQuestionTransformer($params), ResourceTypesEnums::QUESTION);
                break;
            case QuestionTypesEnums::SCALE_RATING:
                return $this->item($question, new StarRatingQuestionTransformer($params), ResourceTypesEnums::QUESTION);
                break;
            case QuestionTypesEnums::MATRIX:
                return $this->item($question, new MatrixQuestionTransformer($params), ResourceTypesEnums::QUESTION);
                break;
            case QuestionTypesEnums::SATISFICATION_RATING:
                return $this->item($question, new SatisfactionRatingQuestionTransformer($params), ResourceTypesEnums::QUESTION);
                break;
            case QuestionTypesEnums::ESSAY_QUESTION:
                return $this->item($question, new EssayQuestionTransformer($params), ResourceTypesEnums::QUESTION);
                break;
        }
    }
}
