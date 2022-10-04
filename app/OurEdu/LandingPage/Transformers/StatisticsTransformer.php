<?php

namespace App\OurEdu\LandingPage\Transformers;

use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class StatisticsTransformer extends TransformerAbstract
{
    public function transform($data)
    {
        $attr = [
            'id' => Str::uuid(),
        ];

        foreach ($data as $key => $value) {
            $attr[$key] = $value;
        }

        return $attr;
    }
}
