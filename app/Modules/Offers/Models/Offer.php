<?php

namespace App\Modules\Offers\Models;

use App\Modules\Courses\Models\Course;
use App\Modules\Courses\Models\OfferCourse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function courses(){
        return $this->hasManyThrough(Course::class, OfferCourse::class, 'offer_id', 'id', 'id', 'course_id');
    }
}
