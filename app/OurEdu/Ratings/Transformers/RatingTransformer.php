<?php

namespace App\OurEdu\Ratings\Transformers;

use App\OurEdu\Ratings\Rating;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Users\Transformers\UserTransformer;

class RatingTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [
        'user',
        'instructor',
    ];

    private $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform(Rating $rating)
    {
        $transformerDatat = [
            'id' => (int) $rating->id,
            'rating' => (float) $rating->rating,
            'comment' => (string) $rating->comment,
        ];

        return $transformerDatat;
    }

    public function includeUser(Rating $rating)
    {
        if ($rating->user) {
            return $this->item($rating->user, new UserTransformer($this->params), ResourceTypesEnums::USER);
        }
    }

    public function includeInstructor(Rating $rating)
    {
        if ($rating->instructor) {
            return $this->item($rating->instructor, new UserTransformer($this->params), ResourceTypesEnums::USER);
        }
    }
}
