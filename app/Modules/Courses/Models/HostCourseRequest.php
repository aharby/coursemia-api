<?php

namespace App\Modules\Courses\Models;

use App\Modules\Countries\Models\Country;
use App\Modules\Specialities\Models\Speciality;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HostCourseRequest extends Model
{
    use HasFactory;

    protected $table = 'host_course_requests';
    protected $fillable = [
        'name',
        'mobile',
        'email',
        'about_course',
        'country_id',
        'speciality_id',
    ];


    public function ScopeFilter($query)
    {
        $query->when(request()->get('searchKey') != '', function ($query) {
            $query->where('name', '%' . request()->get('searchKey') . '%')
                ->orWhere('mobile', '%' . request()->get('searchKey') . '%')
                ->orWhere('email', '%' . request()->get('searchKey') . '%')
                ->orWhere('about_course', '%' . request()->get('searchKey') . '%')
                ->orWhereHas('country', function ($q) {
                    $q->whereTranslationLike('title', '%' . request()->get('searchKey') . '%');
                })
                ->orWhereHas('speciality', function ($q) {
                    $q->whereTranslationLike('title', '%' . request()->get('searchKey') . '%');
                });
        });
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function speciality()
    {
        return $this->belongsTo(Speciality::class, 'speciality_id');
    }
}
