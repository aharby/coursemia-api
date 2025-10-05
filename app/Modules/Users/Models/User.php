<?php

namespace App\Modules\Users\Models;

use App\Modules\Countries\Models\Country;
use App\UserFollow;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Modules\Users\Traits\Invitable;
use App\Modules\Users\Traits\UserRatingable;
use App\Modules\BaseApp\Traits\HasAttach;
use App\Modules\Post\Models\Post;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, HasApiTokens;
    use HasAttach, Notifiable, Invitable, UserRatingable;
    use HasApiTokens; //passport auth
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'full_name',
        'photo',
        'referer_id',
        'country_code',
        'refer_code',
        'email',
        'phone',
        'password',
        'country_id',
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

    public function followers(){
        return $this->hasMany(UserFollow::class, 'followed_id');
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

    public function devices(){
        return $this->hasMany(UserDevice::class);
    }

    public function routeNotificationForFcm($notification)
    {
        return $this->devices()->pluck('device_token')->toArray();
    }

    protected static $attachFields = [
        'profile_picture' => [
            'sizes' => ['small/users/profiles' => 'crop,400x300', 'large/users/profiles' => 'resize,800x600'],
            'path' => 'uploads'
        ],
    ];

    protected $auditExclude = [
        'password',
        'profile_picture'
    ];


    public function posts(){
        return $this->hasMany(Post::class);
    }
    public function scopeActive($query)
    {
        return $query->where('is_active', 1)->where('confirmed', 1);
    }

    public function scopeNotSuperAdmin($query)
    {
        return $query->where('super_admin', '=', 0);
    }

    public function scopeWithoutLoggedUser($query)
    {
        return $query->where('id', '!=', auth()->id());
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'language' => $this->language,
            'type' => $this->type,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'address' => $this->address,
            'is_admin' => $this->is_admin,
            'is_active' => $this->is_active,
            'suspended_at' => $this->suspended_at,
        ];
    }

    // relashionship with role-specific models
        public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function assistant()
    {
        return $this->hasOne(Assistant::class);
    }

    public function instructor()
    {
        return $this->hasOne(Instructor::class);
    }

}

