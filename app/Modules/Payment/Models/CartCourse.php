<?php

namespace App\Modules\Payment\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Modules\Courses\Models\Course;

class CartCourse extends Model
{
    use HasFactory;

    protected $table = 'cart_courses';

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

}
