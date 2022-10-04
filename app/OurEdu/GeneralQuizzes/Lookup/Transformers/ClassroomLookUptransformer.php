<?php


namespace App\OurEdu\GeneralQuizzes\Lookup\Transformers;

use League\Fractal\TransformerAbstract;

class ClassroomLookUptransformer extends TransformerAbstract
{

    protected array $availableIncludes = [
    ];

    /**
     * @return array
     */
    public function transform($classroom)
    {
        return [
            'id' => $classroom->id,
            'name' => $classroom->name,
        ];
    }
}
