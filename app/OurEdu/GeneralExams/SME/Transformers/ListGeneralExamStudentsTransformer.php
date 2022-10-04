<?php

namespace App\OurEdu\GeneralExams\SME\Transformers;

use App\OurEdu\Users\Models\Student;
use League\Fractal\TransformerAbstract;

class ListGeneralExamStudentsTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [
    ];

    public function transform(Student $student)
    {
        $transformerDatat = [
            'id' => (int)$student->id,
            'name' => (string) $student->user->name,
            'result_percentage' => $student->pivot->result ?? 0,
        ];

        return $transformerDatat;
    }
}
