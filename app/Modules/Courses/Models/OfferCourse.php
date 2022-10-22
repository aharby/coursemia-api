<?php

namespace App\Modules\Courses\Models;

use App\Modules\Offers\Models\Offer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferCourse extends Model
{
    use HasFactory;

    public function offer(){
        return $this->belongsTo(Offer::class);
    }

    public function course(){
        return $this->belongsTo(Course::class);
    }
}
