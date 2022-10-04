<?php

namespace App\OurEdu\Courses\Transformers;

use App\OurEdu\Courses\Enums\CourseEnums;
use App\OurEdu\Courses\Models\Course;
use League\Fractal\TransformerAbstract;

class CourseRatingsTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [

    ];

    protected array $availableIncludes = [
    ];
    public function transform(Course $course)
    {
        $ratingsCollection = get_ratings(get_class($course), $course->id);
        $totalRate = 0.00;
        if (!$ratingsCollection->isEmpty()) {
            $ratingsCount = $ratingsCollection->count() == 0 ? 1 : $ratingsCollection->count();
            $totalRate = $ratingsCollection->avg('rating');
            $ratings = [];
            for ($starsNum = CourseEnums::TOTAL_STARS; $starsNum > 0; $starsNum-- ) {
                $percentage = get_percentage($ratingsCollection
                    ->where('rating','>=', $starsNum)
                    ->where('rating','<', $starsNum+1)->count(),
                    $ratingsCount);
                $ratings[] = [
                    'stars' =>  (int) $starsNum,
                    'totalStars' => (int) CourseEnums::TOTAL_STARS,
                    'percent' => (string) number_format($percentage,2,".",""),
                    'progress' => (int) $percentage / 10,
                    "total_students_ratings" => (int) $ratingsCollection->filter(function ($value, $key) use($starsNum) {
                        return round($value->rating) == $starsNum;
                    })->count(),
                ];
            }
        }
        $transformedData = [
            'id' => $course->id,
            'ratings' => $ratings ?? [],
            'total' => [
                'total' => round($totalRate ?? 0.00,1),
                'totalStars' => (int) CourseEnums::TOTAL_STARS,
                'stars' => round($totalRate ??0.00,1),
                "total_students_ratings" => (int)$ratingsCollection->count(),
            ]
        ];

        return $transformedData;
    }

}
