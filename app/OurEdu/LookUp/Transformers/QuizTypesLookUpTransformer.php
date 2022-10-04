<?php


namespace App\OurEdu\LookUp\Transformers;


use App\OurEdu\SchoolAccounts\ClassroomClass\ClassroomClass;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class QuizTypesLookUpTransformer extends TransformerAbstract
{

    protected array $availableIncludes = [
    ];

    public function transform($type)
    {
        return [
            'id' => Str::uuid(),
            'type' => $type
        ];
    }
}
