<?php


namespace App\OurEdu\Subjects\SME\Transformers\EditResources\Resources;


use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\LearningResources\Transformers\LearningResourceAcceptCriteriaGetTransformer;
use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropData;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseData;
use App\OurEdu\Subjects\Models\Subject;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class DragDropDataTransformer extends TransformerAbstract
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
     * @param DragDropData $dragDropData
     * @return array
     */
    public function transform(DragDropData $dragDropData)
    {

        $questions = [];
        foreach ($dragDropData->questions as $question) {


            $questions[] = [
                'id' => $question->id,
                'question' => $question->question,
                'media'=> (object) questionMedia($question),
                'answers' => $question->correct_option_id,
                'audio_link' => $question->audio_link ?? null,
                'video_link' => $question->video_link ?? null,
                'audio' => (object)questionAudio($question),
                'video' => (object)questionVideo($question),
            ];

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
            'description' => $dragDropData->description,
            'question_feedback' => $dragDropData->question_feedback,
            'questions' => $questions,
            'options' => $options

        ];
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


}

