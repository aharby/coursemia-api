<?php

namespace App\OurEdu\Assessments\Transformers\QuestionsTransformers\Matrix;

use App\OurEdu\Assessments\Models\AssessmentAnswerDetails;
use App\OurEdu\Assessments\Models\Questions\Matrix\AssessmentMatrixRow;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class MatrixQuestionRowsTransformer extends TransformerAbstract
{
    protected array $availableIncludes = ['answersCount'];

    public function transform(AssessmentMatrixRow $row)
    {
        return [
            "id" => (int)$row->id,
            "text" => (string)$row->text
        ];
    }

    public function includeAnswersCount(AssessmentMatrixRow $row)
    {
        $details = $row->answerDetail()->selectRaw('option_id, COUNT(option_id) AS options_count')
            ->groupBy(['option_id'])->get();

        return $this->collection(
            $details,
            function (AssessmentAnswerDetails $detail) {
                return [
                    "id" => Str::uuid(),
                    'column_id' => $detail->option_id,
                    'answers_count' => $detail->options_count
                ];
            },
            'answers_count'
        );
    }
}
