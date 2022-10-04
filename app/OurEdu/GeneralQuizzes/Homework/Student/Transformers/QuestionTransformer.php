<?php


namespace App\OurEdu\GeneralQuizzes\Homework\Student\Transformers;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralQuizzes\Enums\QuestionsTypesEnums;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Transformers\QuestionsTransformers\DragDropQuestionTransformer;
use App\OurEdu\GeneralQuizzes\Transformers\QuestionsTransformers\EssayQuestionTransformer;
use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropData;
use App\OurEdu\GeneralQuizzes\Transformers\QuestionsTransformers\CompleteQuestionTransformer;
use App\OurEdu\ResourceSubjectFormats\Models\QuestionHeadInterface;
use League\Fractal\TransformerAbstract;
use App\OurEdu\GeneralQuizzes\Transformers\QuestionsTransformers\MultipleChoiceQuestionTransformer;
use App\OurEdu\GeneralQuizzes\Transformers\QuestionsTransformers\TrueFalseQuestionTransformer;
use App\OurEdu\GeneralQuizzes\Transformers\QuestionsTransformers\TrueFalseQuestionWithChoiceTransformer;
class QuestionTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'question',
    ];
    /**
     * @var array
     */
    private $param;
    private $slug;
    private $questionBank;

    /**
     * QuestionTransformer constructor.
     * @param array $param
     */
    public function __construct(array $param = [])
    {
        $this->param = $param;
    }


    public function transform(QuestionHeadInterface $questionData)
    {
        $this->slug = $questionData->questionBank->slug;
        if ($questionData instanceof DragDropData) {
            $this->questionBank= $questionData->questionBank;
        }
        $questionData = $questionData->questionHead();

        return [
            "id" => (int)$questionData->id,
            'description' => (string)$questionData->description,
            'question_slug' => (string)$this->slug ,
        ];
    }

    public function includeQuestion (QuestionHeadInterface $questionData) {
        switch ($this->slug) {
            case QuestionsTypesEnums::MULTI_CHOICE:
                return $this->item($questionData, new MultipleChoiceQuestionTransformer($this->param), ResourceTypesEnums::QUESTION);

            case QuestionsTypesEnums::SINGLE_CHOICE:
                return $this->item($questionData, new MultipleChoiceQuestionTransformer($this->param), ResourceTypesEnums::QUESTION);

            case QuestionsTypesEnums::TRUE_FALSE:
                return $this->item($questionData, new TrueFalseQuestionTransformer($this->param), ResourceTypesEnums::QUESTION);

            case QuestionsTypesEnums::TRUE_FALSE_WITH_CORRECT:
                return $this->item($questionData, new TrueFalseQuestionWithChoiceTransformer($this->param), ResourceTypesEnums::QUESTION);

            case QuestionsTypesEnums::ESSAY:
                return $this->item($questionData, new EssayQuestionTransformer($this->param), ResourceTypesEnums::QUESTION);
            case QuestionsTypesEnums::DRAG_DROP_TEXT:
            case QuestionsTypesEnums::DRAG_DROP_IMAGE:
                $params = $this->param;
                if($this->questionBank){
                    $params['questionBank']=$this->questionBank;
                }
                return $this->item($questionData, new DragDropQuestionTransformer($params), ResourceTypesEnums::HOMEWORK_QUESTION_DATA);
                break;
            case QuestionsTypesEnums::COMPLETE:
                return $this->item($questionData, new CompleteQuestionTransformer($this->param), ResourceTypesEnums::QUESTION);
        }
    }
}
