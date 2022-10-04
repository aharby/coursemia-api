<?php


namespace App\OurEdu\GeneralQuizzes\Lookup\Transformers;

use League\Fractal\TransformerAbstract;

class SubjectLookUpTransformer extends TransformerAbstract
{

    protected array $availableIncludes = [
    ];


    /**
     * @return array
     */
    public function transform($subject)
    {
        return [
            'id' => $subject->id,
            'name' => $subject->name,
        ];
    }
}
