<?php


namespace App\OurEdu\SchoolAccounts\ClassroomClassSessions\EducationalSupervisor\Transformers;

use App\OurEdu\SchoolAccounts\Classroom;
use League\Fractal\TransformerAbstract;

class ClassroomTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];
    protected array $availableIncludes = [

    ];


    /**
     * @param Classroom $classroom
     * @return array
     */
    public function transform(Classroom $classroom)
    {
        return [
            'id' => (int)$classroom->id,
            'name' => (string)$classroom->name
        ];
    }

}
