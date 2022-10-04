<?php


namespace App\OurEdu\ResourceSubjectFormats\Transformers;

use League\Fractal\TransformerAbstract;
use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceData;

class MultipleChoiceDataTransformer extends TransformerAbstract
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
     * @param MultipleChoiceData $multipleChoiceData
     * @return array
     */
    public function transform(MultipleChoiceData $multipleChoiceData)
    {
        $questions = [];
        foreach ($multipleChoiceData->questions as $question) {
            $options = [];
            foreach ($question->options as $option) {
                $options[] = [
                    'id' => $option->id,
                    'option' => $option->answer,
                    'is_correct_answer' => (bool)$option->is_correct_answer
                ];
            }
            $questions[] = [
                'id' => $question->id,
                'question_type'  =>  LearningResourcesEnums::MULTI_CHOICE,
                'question' => $question->question,
                'url' => $question->url,
                'question_feedback' => $question->question_feedback,
                'media' => (object) questionMedia($question),
                'options' => $options,
            ];
        }
        return [
            'id' => $multipleChoiceData->id,
            'description' => $multipleChoiceData->description,
            'questions' => $questions
        ];
    }
}
