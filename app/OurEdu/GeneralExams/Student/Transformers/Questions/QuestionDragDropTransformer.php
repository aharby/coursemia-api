<?php


namespace App\OurEdu\GeneralExams\Student\Transformers\Questions;


use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropData;
use League\Fractal\TransformerAbstract;

class QuestionDragDropTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [

    ];
    protected array $availableIncludes = [
    ];

    private $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    /**
     * @return array
     */
    public function transform($dragDropData)
    {

        $questions = [];
        foreach ($dragDropData->questions as $question) {

            $questionsData = [
                'id' => $question->id,
                'question' => $question->question,
                'media'=> (object) questionMedia($question)
            ];

            $questions[] = $questionsData;

        }
        $options = [];
        foreach ($dragDropData->options as $option) {


            $options[] = [
                'id' => $option->id,
                'option' => $option->option,
            ];

        }


        return [
            'id' => $dragDropData->id,
            'type' => $dragDropData->question_type,
            'description' => $dragDropData->description,
            'questions' => $questions,
            'options' => $options
        ];
    }

}

