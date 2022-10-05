<?php

namespace App\Modules\Users\Traits;

use App\Modules\Ratings\Rating;
use Illuminate\Database\Eloquent\Model;

trait UserRatingable
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function ratings()
    {
        return $this->hasMany(Rating::class, 'instructor_id');
    }

    /**
     *
     * @return mix
     */
    public function avgRating()
    {
        $score = $this->ratings()->whereNotNull('rating')->avg('rating');

        return number_format($score, 2, '.', '');
    }

    /**
     *
     * @return mix
     */
    public function sumRating()
    {
        $score = $this->ratings()->whereNotNull('rating')->sum('rating');

        return number_format($score, 2, '.', '');
    }

    /**
     * @param $max
     *
     * @return mix
     */
    public function ratingPercent($max = 5)
    {
        $quantity = $this->ratings()->whereNotNull('rating')->count();
        $total = $this->sumRating();
        $score = ($quantity * $max) > 0 ? $total / (($quantity * $max) / 100) : 0;

        return number_format($score, 2, '.', '');
    }

    public function getAvgRatingAttribute()
    {
        return $this->avgRating();
    }

    public function getratingPercentAttribute()
    {
        return $this->ratingPercent();
    }

    public function getSumRatingAttribute()
    {
        return $this->sumRating();
    }
}
