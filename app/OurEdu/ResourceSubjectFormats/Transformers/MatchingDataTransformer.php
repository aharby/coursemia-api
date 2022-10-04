<?php


namespace App\OurEdu\ResourceSubjectFormats\Transformers;

use League\Fractal\TransformerAbstract;
use App\OurEdu\ResourceSubjectFormats\Models\Video\VideoData;
use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\ResourceSubjectFormats\Models\Matching\MatchingData;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseData;

class MatchingDataTransformer extends TransformerAbstract
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
     * @param MatchingData $matchingData
     * @return array
     */
    public function transform(MatchingData $matchingData)
    {
        $questions = [];

        $options = [];

        foreach ($matchingData->questions as $question) {
            $questions[] = [
                'id' => $question->id,
                'question_type' =>  LearningResourcesEnums::MATCHING,
                'text' => $question->text,
                'media' => (object) questionMedia($question)
            ];
        }

        foreach ($matchingData->options as $option) {
            $options[] = [
                'id' => $option->id,
                'option' => $option->option,
                'question_id' => $option->res_matching_question_id
            ];
        }

        return [
            'id' => $matchingData->id,
            'description' => $matchingData->description,
            'question_feedback' => $matchingData->question_feedback,
            'questions' => $questions ,
            'options' => $options
        ];
    }
}
