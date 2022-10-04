<?php


namespace App\OurEdu\ResourceSubjectFormats\Transformers;

use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\ResourceSubjectFormats\Models\HotSpot\HotSpotData;

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
            ];
        }

        return [
            'id' => $hotSpotData->id,
            'description' => $hotSpotData->description,
            'questions' => $questions ,
        ];
    }
}
