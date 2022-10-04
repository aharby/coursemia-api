<?php


namespace App\OurEdu\LookUp\Transformers;


use App\OurEdu\SchoolAccounts\ClassroomClass\ClassroomClass;
use League\Fractal\TransformerAbstract;

class ClassroomClassLookUpTransformer extends TransformerAbstract
{

    protected array $availableIncludes = [
    ];


    /**
     * @param ClassroomClass $class
     * @return array
     */
    public function transform(ClassroomClass $class)
    {
        return [
            'id' => (int)$class->id,
            'classroom_id' => (int)$class->classroom_id
        ];
    }
}
