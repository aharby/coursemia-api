<?php

namespace App\OurEdu\BaseApp\Traits;

use App\OurEdu\Users\User;
use App\OurEdu\Ratings\Rating;
use Illuminate\Database\Eloquent\Model;

trait Ratingable
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function ratings()
    {
        return $this->morphMany(Rating::class, 'ratingable');
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
    
    /**
     * @param $data
     * @param Model      $user
     *
     * @return static
     */
    public function rating($data, Model $user)
    {
        return (new Rating())->createRating($this, $data, $user);
    }

    /**
     * @param $data
     * @param Model      $user
     *
     * @return static
     */
    public function ratingUnique($data, User $user)
    {
        return (new Rating())->createUniqueRating($this, $data, $user);
    }
    
    /**
     * @param $id
     * @param $data
     *
     * @return mixed
     */
    public function updateRating($id, $data)
    {
        return (new Rating())->updateRating($id, $data);
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
