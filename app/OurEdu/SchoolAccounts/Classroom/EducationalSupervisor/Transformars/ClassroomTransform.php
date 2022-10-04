<?php


namespace App\OurEdu\SchoolAccounts\Classroom\EducationalSupervisor\Transformars;


use App\OurEdu\SchoolAccounts\Classroom;
use League\Fractal\TransformerAbstract;

class ClassroomTransform extends TransformerAbstract
{

    public function transform(Classroom $classroom)
    {
        return [
            "id" => $classroom->id,
            "name" => $classroom->name,
        ];
    }

}
