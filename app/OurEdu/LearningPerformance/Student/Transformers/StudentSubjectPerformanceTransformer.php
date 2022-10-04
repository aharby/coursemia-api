<?php


namespace App\OurEdu\LearningPerformance\Student\Transformers;



use App\OurEdu\LearningPerformance\LearningPerformance;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class StudentSubjectPerformanceTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];



    public function transform(LearningPerformance $learningPerformance)
    {

        return [
            'id' => Str::uuid(),


        ];
    }


    public static function originalAttribute($index)
    {
        $attributes = [
            'id' => 'id',
        ];
        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

    public static function transformedAttribute($index)
    {
        $attributes = [
            'id' => 'id',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}

