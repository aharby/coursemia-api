<?php

namespace App\Modules\Users\Models;

use Illuminate\Database\Eloquent\Model;

use App\Modules\Courses\Models\Course;
use App\Modules\Users\Models\User;  
use App\Modules\Payment\Models\Order;
use App\Modules\Payment\Models\CartCourse;

class Student extends Model
{
    protected $fillable = ['user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'student_courses');
    }

    public function isEnrolledIn($courseId)
    {
        return $this->courses()->where('course_id', $courseId)->exists();
    }

    public function getRankAttribute(){
        $higherUsers = 0;
        
        if(array_key_exists('total_correct_answers', $this->attributes))
        {
            $higherUsers = $this->where('total_correct_answers', '>', $this->attributes['total_correct_answers'])->count();
        }

        return $higherUsers+1;
    }


    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    
    public function cartCourses()
    {
        return $this->hasMany(CartCourse::class);
    }
}
