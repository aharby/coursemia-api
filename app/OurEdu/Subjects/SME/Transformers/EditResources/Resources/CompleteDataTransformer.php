<?php

namespace App\OurEdu\Subjects\SME\Transformers\EditResources\Resources;

use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\ResourceSubjectFormats\Models\Complete\CompleteData;
use App\OurEdu\Exams\Student\Transformers\Questions\CompleteQuestionTransformer;

class CompleteDataTransformer extends TransformerAbstract
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
     * @param CompleteData $completeData
     * @return array
     */
    public function transform(CompleteData $completeData)
    {
        $questions = [];
        foreach ($completeData->questions as $question) {
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
        }

        return [
            'id' => $completeData->id,
            'description' => $completeData->description,
            'questions' => $questions ,
        ];
    }
}
