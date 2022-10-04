<?php


namespace App\OurEdu\ResourceSubjectFormats\Transformers;

use League\Fractal\TransformerAbstract;
use App\OurEdu\ResourceSubjectFormats\Models\Video\VideoData;
use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseData;
use App\OurEdu\ResourceSubjectFormats\Models\MultiMatching\MultiMatchingData;

class MultiMatchingDataTransformer extends TransformerAbstract
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
     * @param MultiMatchingData $multiMatchingData
     * @return array
     */
    public function transform(MultiMatchingData $multiMatchingData)
    {
        $questions = [];
        $options = [];
        foreach ($multiMatchingData->questions as $question) {
            $questions[] = [
                'id' => $question->id,
                'text' => $question->text,
                'question_type' =>  LearningResourcesEnums::MULTIPLE_MATCHING,
                'media' => (object) questionMedia($question)
            ];
        }

        foreach ($multiMatchingData->options as $option) {
            $options[] = [
                'id' => $option->id,
                'option' => $option->option,
                'questions' => $option->questions()->pluck('res_multi_matching_questions.id')->toArray()
            ];
        }

        return [
            'id' => $multiMatchingData->id,
            'description' => $multiMatchingData->description,
            'question_feedback' => $multiMatchingData->question_feedback,
            'questions' => $questions ,
            'options' => $options

        ];
    }
}
