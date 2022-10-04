<?php


namespace App\OurEdu\LookUp\Transformers;


use App\OurEdu\SchoolAccounts\ClassroomClass\ClassroomClass;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class QuizTimesLookUpTransformer extends TransformerAbstract
{

    protected array $availableIncludes = [
    ];

    public function transform($time)
    {
        return [
            'id' => Str::uuid(),
            'time' => $time
        ];
    }
}
