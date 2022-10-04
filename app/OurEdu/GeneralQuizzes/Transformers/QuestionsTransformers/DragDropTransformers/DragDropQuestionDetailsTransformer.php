<?php

namespace App\OurEdu\GeneralQuizzes\Transformers\QuestionsTransformers\DragDropTransformers;

use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropQuestion;
use League\Fractal\TransformerAbstract;

class DragDropQuestionDetailsTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];

    /**
     * @var array
     */
    private $params;

    /**
     * MultipleChoiceQuestionAnswerTransformer constructor.
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->params = $params;
    }

    public function transform(DragDropQuestion $question)
    {
        $data = [
            'id' => (int)$question->id,
            'question' => (string)$question->question,
            'media' => (object)questionMedia($question)
        ];
        if (!isset($this->params['student'])) {
            $data['answers'] = (int)$question->correct_option_id;
        }
        return $data;
    }
}
