<?php

namespace App\OurEdu\GeneralExams\SME\Transformers\Questions;

use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\ResourceSubjectFormats\Models\Complete\CompleteData;
use App\OurEdu\Exams\Student\Transformers\Questions\CompleteQuestionTransformer;

class CompleteDataTransformer extends TransformerAbstract
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
     * @param CompleteData $completeData
     * @return array
     */
    public function transform(CompleteData $completeData)
    {
        $questions = [];
        $question = $completeData->questions()->findOrFail($this->params['questionId']);
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
            'description' => $completeData->description,
            'questions' => $questions ,
        ];
    }

    public function includeQuestions(CompleteData $completeData)
    {
        if ($completeData->questions) {
            return $this->collection($completeData->questions, new CompleteQuestionTransformer(['is_answer' => true]), ResourceTypesEnums::COMPLETE_QUESTION);
        }
    }
}
