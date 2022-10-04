<?php

namespace App\OurEdu\Courses\Transformers;

use App\OurEdu\Courses\Enums\CourseEnums;
use App\OurEdu\Ratings\Rating;
use League\Fractal\TransformerAbstract;

class CourseRatingDetailsTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [

    ];

    protected array $availableIncludes = [
    ];


    public function __construct()
    {
    }

    public function transform(Rating $courseRating)
    {
        $transformedData = [
            'id' => $courseRating->id,
            'image' => (string) imageProfileApi($courseRating->user->profile_picture , 'small'),
            'details' => $courseRating->comment,
            'name' => $courseRating->user->name,
            'stars' => (float)$courseRating->rating ,
            'totalStars' => (int) CourseEnums::TOTAL_STARS,
            'date' => (string)$courseRating->created_at,
        ];
        return $transformedData;
    }

}
