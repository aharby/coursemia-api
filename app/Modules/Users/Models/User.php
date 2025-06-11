<?php

namespace App\Modules\Users\Models;

use App\Modules\Countries\Models\Country;
use App\Modules\Courses\Models\Course;
use App\Modules\Courses\Models\CourseUser;
use App\UserFollow;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Passport\HasApiTokens;
use App\Modules\Payment\Models\Order;
use App\Modules\Payment\Models\CartCourse;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_verified'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function country(){
        return $this->belongsTo(Country::class);
    }

    public function devices(){
        return $this->hasMany(UserDevice::class);
    }

    public function courses(){
        return $this->hasManyThrough(Course::class, CourseUser::class,'user_id', 'id', 'id', 'course_id');
    }

    public function followers(){
        return $this->hasMany(UserFollow::class, 'followed_id');
    }

    public function getRankAttribute(){
        $higherUsers = 0;
        
        if(array_key_exists('total_correct_answers', $this->attributes))
        {
            $higherUsers = \App\Modules\Users\Models\User::where('total_correct_answers', '>', $this->attributes['total_correct_answers'])->count();
        }

        return $higherUsers+1;
    }

    public function ScopeSorter($query)
    {
        $query->when(request()->has('sortBy'), function ($quer) {
            $sortByDir = request()->get('sortDesc') == 'true' ? "desc" : "asc";
            switch (request()->get('sortBy')) {
                case 'user':
                    $quer->orderBy('full_name', $sortByDir);
                    break;
                case 'email':
                    $quer->orderBy('email', $sortByDir);
                    break;
                default:
                    $quer->orderBy('id', $sortByDir);
            }
        });
    }

    public function routeNotificationForFcm($notification)
    {
        return $this->devices()->pluck('device_token')->toArray();
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

