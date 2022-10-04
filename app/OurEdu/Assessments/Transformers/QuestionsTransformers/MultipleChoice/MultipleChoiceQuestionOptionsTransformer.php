<?php

namespace App\OurEdu\Assessments\Transformers\QuestionsTransformers\MultipleChoice;

use App\OurEdu\Assessments\Models\AssessmentAnswerDetails;
use App\OurEdu\Assessments\Models\Questions\MultipleChoice\AssessmentMultipleChoiceOptions;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class MultipleChoiceQuestionOptionsTransformer extends TransformerAbstract
{
    protected array $availableIncludes = ['answersCount'];

    public function transform(AssessmentMultipleChoiceOptions $option)
    {
        return [
            "id" => (int)$option->id,
            "option" => (string)$option->answer,
            'grade' => (float)$option->grade
        ];
    }

    public function includeAnswersCount(AssessmentMultipleChoiceOptions $option)
    {
        $detail = $option->answerDetail()->selectRaw('option_id, COUNT(option_id) AS options_count')
            ->groupBy(['option_id'])->first();

        if (!is_null($detail)) {
            return $this->item(
                $detail,
                function (AssessmentAnswerDetails $detail) {
                    return [
                        "id" => Str::uuid(),
                        "option_id" => (int)$detail->option_id,
                        'answers_count' => $detail->options_count
                    ];
                },
                'answers_count'
            );
        }
        return null;
    }
}
