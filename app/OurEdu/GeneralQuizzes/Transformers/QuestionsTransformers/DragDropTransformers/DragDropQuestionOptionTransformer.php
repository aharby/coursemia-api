<?php

namespace App\OurEdu\GeneralQuizzes\Transformers\QuestionsTransformers\DragDropTransformers;

use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropOption;
use League\Fractal\TransformerAbstract;

class DragDropQuestionOptionTransformer extends TransformerAbstract
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

    public function transform(DragDropOption $option)
    {
        return [
            'id' => (int)$option->id,
            'option' => (string)$option->option,
        ];
    }
}
