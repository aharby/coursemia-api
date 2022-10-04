<?php

namespace App\OurEdu\Ratings;

use App\OurEdu\Users\User;
use App\OurEdu\BaseApp\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rating extends BaseModel
{

    use SoftDeletes, HasFactory;
    
    /**
     * @var string
     */
    protected $table = 'ratings';

    /**
     * @var array
     */
    protected $fillable = ['rating', 'ratingable_id' , 'ratingable_type' , 'user_id', 'instructor_id', 'comment'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function ratingable()
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }


    /**
     * @param Model $ratingable
     * @param $data
     * @param Model $user
     *
     * @return static
     */
    public function createRating(Model $ratingable, $data)
    {
        $rating = new static();
        $rating->fill($data);

        $ratingable->ratings()->save($rating);

        return $rating;
    }

    /**
     * @param Model $ratingable
     * @param $data
     * @param User $author
     *
     * @return static
     */
    public function createUniqueRating(Model $ratingable, $data, User $user)
    {
        $rating = [
            'user_id' => $user->id,
            "ratingable_id" => $ratingable->id,
            "ratingable_type" => get_class($ratingable),
        ];

        Rating::updateOrCreate($rating, $data);
        return $rating;
    }

    /**
     * @param $id
     * @param $data
     *
     * @return mixed
     */
    public function updateRating($id, $data)
    {
        $rating = static::find($id);
        $rating->update($data);

        return $rating;
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function deleteRating($id)
    {
        return static::find($id)->delete();
    }
}
