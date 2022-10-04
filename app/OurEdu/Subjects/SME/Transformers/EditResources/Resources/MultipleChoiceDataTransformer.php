<?php


namespace App\OurEdu\Subjects\SME\Transformers\EditResources\Resources;


use App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceData;
use League\Fractal\TransformerAbstract;

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

    public static function originalAttribute($index)
    {
        $attributes = [
            'id' => 'id',
            'date' => 'date',
            'SN' => 'SN'
        ];
        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

    public static function transformedAttribute($index)
    {
        $attributes = [
            'id' => 'id',
            'date' => 'date',
            'SN' => 'SN'
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
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
                'question' => $question->question,
                'question_feedback' => $question->question_feedback,
                'media' => (object) questionMedia($question),
                'audio' => (object)questionAudio($question),
                'video' => (object)questionVideo($question),
                'audio_link' => $question->audio_link ?? null,
                'video_link' => $question->video_link ?? null,
                'options' => $options,
            ];
        }
        return [
            'id' => $multipleChoiceData->id,
            'description' => $multipleChoiceData->description,
            'multiple_choice_type' => $multipleChoiceData->multipleChoiceType->slug,
            'questions' => $questions
        ];
    }


}

