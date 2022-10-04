<?php

namespace App\OurEdu\Assessments\Transformers\QuestionsTransformers\Matrix;

use App\OurEdu\Assessments\Models\Questions\Matrix\AssessmentMatrixColumn;
use League\Fractal\TransformerAbstract;

class MatrixQuestionColumnsTransformer extends TransformerAbstract
{
    public function transform(AssessmentMatrixColumn $column)
    {
        $transformedData = [
            "id" => (int)$column->id,
            "text" => (string)$column->text,
            'grade'=>(float)$column->grade,
        ];
        return $transformedData;
    }
}
