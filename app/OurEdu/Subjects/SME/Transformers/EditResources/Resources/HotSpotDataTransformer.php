<?php


namespace App\OurEdu\Subjects\SME\Transformers\EditResources\Resources;


use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\ResourceSubjectFormats\Models\HotSpot\HotSpotData;
use League\Fractal\TransformerAbstract;

class HotSpotDataTransformer extends TransformerAbstract
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
     * @param HotSpotData $hotSpotData
     * @return array
     */
    public function transform(HotSpotData $hotSpotData)
    {

        $questions = [];
        foreach ($hotSpotData->questions as $question) {
            $answers = [];
            foreach ($question->answers as $answer) {
                $answers[] = [
                    'id'    => $answer->id,
                    'answer' => json_decode($answer->answer)
                ];
            }
            $questions[] = [
                'id' => $question->id,
                'question_type'  =>  LearningResourcesEnums::HOTSPOT,
                'question' => $question->question,
                'question_feedback' => $question->question_feedback,
                'image_width' => $question->image_width,
                'media' => (object) questionMedia($question),
                'answers' => $answers,
                'audio_link' => $question->audio_link ?? null,
                'video_link' => $question->video_link ?? null,
                'audio' => (object)questionAudio($question),
                'video' => (object)questionVideo($question),
            ];
        }

        return [
            'id' => $hotSpotData->id,
            'description' => $hotSpotData->description,
            'questions' => $questions ,
        ];
    }

}

