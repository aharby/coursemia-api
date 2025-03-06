<?php

namespace App\Modules\Courses\Models;

use App\Modules\Users\Admin\Models\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class CourseLecture extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function course(){
        return $this->belongsTo(Course::class);
    }

    public function getTitleAttribute(){
        $lang = app()->getLocale();
        return $this->attributes["title_$lang"];
    }

    public function getDescriptionAttribute(){
        $lang = app()->getLocale();
        return $this->attributes["description_$lang"];
    }

    public function ScopeSorter($query){
        $query->when(request()->has('sortBy'), function ($quer) {
            $sortByDir = request()->get('sortDesc') == 'true' ? "desc" : "asc";
            switch (request()->get('sortBy')) {
                case 'title_en':
                    $quer->orderBy('title_en', $sortByDir);
                    break;
                default:
                    $quer->orderBy('id', $sortByDir);
            }
        });
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function progress($user_id)
    {
        return $this->hasOne(LectureProgress::class)
        ->where('user_id', $user_id)->first();
    }
}
