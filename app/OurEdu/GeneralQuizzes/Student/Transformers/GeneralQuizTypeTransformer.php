<?php

namespace App\OurEdu\GeneralQuizzes\Student\Transformers;

use League\Fractal\TransformerAbstract;

class GeneralQuizTypeTransformer extends TransformerAbstract
{
    public function transform(array $quizType): array
    {
        return [
            'id' => $quizType['key'],
            'name' => $quizType['type']
        ];
    }
}
