<?php

namespace App\OurEdu\Exams\Instructor\Transformers;

use App\OurEdu\Exams\Models\Exam;
use League\Fractal\TransformerAbstract;

class CourseTransformer extends TransformerAbstract
{

    public function transform($data)
    {
        return [
            'id' => $data->id,
            'name' => $data->name
        ];

    }


}
