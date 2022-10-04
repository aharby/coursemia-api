<?php


namespace App\OurEdu\Assessments\Assessor\Transformers;

use App\OurEdu\Assessments\Transformers\QuestionsTransformers\EssayQuestion\EssayQuestionTransformer;
use App\OurEdu\Assessments\Transformers\QuestionsTransformers\SatisfactionRating\SatisfactionRatingQuestionTransformer;
use App\OurEdu\Assessments\Transformers\QuestionsTransformers\StarRating\StarRatingQuestionTransformer;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\Assessments\Models\AssessmentQuestion;
use League\Fractal\TransformerAbstract;
use App\OurEdu\Assessments\Enums\QuestionTypesEnums;
use App\OurEdu\Assessments\Transformers\QuestionsTransformers\MultipleChoice\MultipleChoiceQuestionTransformer;
use App\OurEdu\Assessments\Transformers\QuestionsTransformers\Matrix\MatrixQuestionTransformer;


class AssessmentQuestionTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'questions'
    ];

    // protected array $availableIncludes = ['actions'];
    /**
     * @var Assessment
     */
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
        $transformedData = [
            "id" => (int)$assessmentQuestion->id,
            'question_type' => (string)$assessmentQuestion->slug,
            'skip_question' => (bool) $assessmentQuestion->skip_question,
            'category'      => $assessmentQuestion->category ? $assessmentQuestion->category->title :''
        ];
        if(isset($this->params['nextBack'])){
            $transformedData['introduction'] = $this->assessment->introduction;
            $transformedData['has_general_comment'] = $this->assessment->has_general_comment ? true :false;
        }

        if($assessmentQuestion->slug != QuestionTypesEnums::ESSAY_QUESTION) {
            $transformedData['question_grade'] = (float)$assessmentQuestion->question_grade;
        }

        return $transformedData;
    }

    public function includeQuestions(AssessmentQuestion $assessmentQuestion)
    {
        $question = $assessmentQuestion->question;
        $params = $this->params;
        $params['assessment'] = $this->assessment;
        $params['question_grade'] = $assessmentQuestion->question_grade;
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


    public function includeActions(AssessmentQuestion $assessmentQuestion)
    {

        if (isset($this->params['finish_assessment'])) {
            $actions[] = [

                'endpoint_url' => buildScopeRoute('api.assessments.assessor.post.finish', ['assessment' => $this->assessment]),
                'label' => trans('assessment.Finish assessment'),
                'method' => 'POST',
                'key' => APIActionsEnums::FINISH_ASSESSMENT
            ];
        }

        $page = request()->input('page') ?? 1;

        $actions[] = [
            'endpoint_url' => buildScopeRoute(
                'api.assessments.assessor.post.answer', [
                    'assessment' => $this->assessment,
                    'page'=>$page
                ]
            ),


            'label' => trans('assessment.Post answer'),
            'method' => 'POST',
            'key' => APIActionsEnums::POST_ANSWER
        ];
        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }
}
