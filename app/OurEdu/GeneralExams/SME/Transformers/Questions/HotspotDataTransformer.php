<?php

namespace App\OurEdu\GeneralExams\SME\Transformers\Questions;

use App\OurEdu\Exams\Student\Transformers\Questions\HotspotQuestionTransformer;
use App\OurEdu\ResourceSubjectFormats\Models\HotSpot\HotSpotData;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;

class HotspotDataTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [
        'questions'
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
        $question = $hotSpotData->questions()->findOrFail($this->params['questionId']);
        $answers = [];

        foreach ($question->acceptedAnswers as $answer) {
            $answers[] = [
                'id'    => $answer->id,
                'answer' => $answer->answer
            ];
        }

        $questions[] = [
            'id' => $question->id,
            'question_type'  =>  LearningResourcesEnums::COMPLETE,
            'question' => $question->question,
            'question_feedback' => $question->question_feedback,
            'answer' => $question->answer->answer ?? null,
            'accepted_answers' => $answers,
        ];

        return [
            'id' => Str::uuid(),
            'description' => $hotSpotData->description,
            'questions' => $questions ,
        ];
    }

    public function includeQuestions(HotSpotData $hotSpotData)
    {
        if ($hotSpotData->questions) {
            return $this->collection($hotSpotData->questions, new HotspotQuestionTransformer(['is_answer' => true]), ResourceTypesEnums::COMPLETE_QUESTION);
        }
    }
}
