<?php


namespace App\OurEdu\Reports\Parent\Transformers;


use App\OurEdu\Subjects\Models\Subject;
use League\Fractal\TransformerAbstract;

class SubjectTransformer extends TransformerAbstract
{
    public function transform(Subject $subject) {
        return [
            "id" => $subject->id,
            "name" => $subject->name
        ];
    }
}
