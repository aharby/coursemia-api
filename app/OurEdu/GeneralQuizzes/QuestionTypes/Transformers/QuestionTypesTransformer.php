<?php

namespace App\OurEdu\GeneralQuizzes\QuestionTypes\Transformers;

use League\Fractal\TransformerAbstract;
use Illuminate\Support\Str;


class QuestionTypesTransformer extends TransformerAbstract
{

    public function transform($types)
    {
    
        $data = [
            'id' => Str::uuid(),
        ];

        return  array_merge($types,$data);
    }

}
