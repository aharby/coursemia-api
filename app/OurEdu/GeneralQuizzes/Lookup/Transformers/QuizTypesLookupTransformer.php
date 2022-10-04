<?php

namespace App\OurEdu\GeneralQuizzes\Lookup\Transformers;

use App\OurEdu\Quizzes\Enums\QuizTypesEnum;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class QuizTypesLookupTransformer extends TransformerAbstract
{
    public function transform($type)
    {
        return [
            'id' => Str::uuid(),
            'type' => $type,
            'label' => QuizTypesEnum::getLabel($type),
        ];
    }
}
