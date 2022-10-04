<?php


namespace App\OurEdu\GeneralQuizzes\Lookup\Transformers;

use League\Fractal\TransformerAbstract;

class GradeClassLookUpTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [];

    protected array $availableIncludes = [
    ];

    /**
     * @param $class
     * @return array
     */
    public function transform($class)
    {
        return [
            'id' => $class->id,
            'name' => $class->title
        ];
    }
}


