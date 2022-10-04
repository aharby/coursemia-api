<?php


namespace App\OurEdu\SchoolAccounts\ClassroomClassSessions\SchoolSupervisor\Transformers;

use App\OurEdu\SchoolAccounts\Classroom;
use App\OurEdu\SchoolAccounts\SubjectSchoolInstructor;
use League\Fractal\TransformerAbstract;

class InstructorTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];
    protected array $availableIncludes = [

    ];


    /**
     * @param $instructor
     * @return array
     */
    public function transform( $instructor)
    {
        return [
            'id' => (int)$instructor->id,
            'name' => (string)$instructor->first_name.' '.$instructor->last_name,
            'first_name' => (string)$instructor->first_name,
            'last_name' => (string)$instructor->last_name,
        ];
    }

}
