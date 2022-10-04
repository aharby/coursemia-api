<?php


namespace App\OurEdu\LookUp\Transformers;


use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class ClassroomLookUpTransformer extends TransformerAbstract
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
