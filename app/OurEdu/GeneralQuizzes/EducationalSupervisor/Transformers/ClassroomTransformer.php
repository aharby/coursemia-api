<?php


namespace App\OurEdu\GeneralQuizzes\EducationalSupervisor\Transformers;


use App\OurEdu\SchoolAccounts\Classroom;
use League\Fractal\TransformerAbstract;

class ClassroomTransformer extends TransformerAbstract
{

    public function transform(Classroom $classroom)
    {
        return [
            "id" => $classroom->id,
            "name" => $classroom->name,
        ];
    }

}
