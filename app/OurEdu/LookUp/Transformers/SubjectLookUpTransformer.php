<?php


namespace App\OurEdu\LookUp\Transformers;


use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class SubjectLookUpTransformer extends TransformerAbstract
{

    protected array $availableIncludes = [
    ];

    private $param;



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
