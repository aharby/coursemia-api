<?php

namespace App\OurEdu\Assessments\Transformers\QuestionsTransformers\StarRating;

use App\OurEdu\Assessments\Models\AssessmentAnswerDetails;
use App\OurEdu\Assessments\Models\Questions\Rating\AssissmentRatingOptions;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class StarRatingQuestionOptionsTransformer extends TransformerAbstract
{
    protected array $availableIncludes = ['answersCount'];

    public function transform(AssissmentRatingOptions $option)
    {
        return [
            "id" => (int)$option->id,
            "option" => (string)$option->answer,
            'grade' => (float)$option->grade,
            'order' => (int)$option->order,
        ];
    }

    public function includeAnswersCount(AssissmentRatingOptions $option)
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
