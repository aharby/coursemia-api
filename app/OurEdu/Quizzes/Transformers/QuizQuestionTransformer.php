<?php

namespace App\OurEdu\Quizzes\Transformers;

use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Quizzes\Models\QuizQuestion;
use League\Fractal\TransformerAbstract;

class QuizQuestionTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];
    protected array $availableIncludes = [
    ];

    protected $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform(QuizQuestion $quizQuestion)
    {
        $transformedData = [
            'id' => (int) $quizQuestion->id,
            'quiz_id' => (string) $quizQuestion->quiz_id,
            'question_type' => (string) $quizQuestion->question_type,
            'question_text' => (string) $quizQuestion->question_text,
            'question_grade' => (integer) $quizQuestion->question_grade,
            'time_to_solve' => (integer) $quizQuestion->time_to_solve,
        ];

        $options = [];
        foreach ($quizQuestion->options as $option) {
            $optionData =
                [
                    'id' => $option->id,
                    'option' => $option->option,
                    'is_correct_answer' => $option->is_correct_answer,
                ];
            $options[] = $optionData;
        }
        $transformedData['options'] = $options;
        return $transformedData;
    }
}
