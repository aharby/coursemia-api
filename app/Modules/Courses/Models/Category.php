<?php

namespace App\Modules\Courses\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    public function getTitleAttribute(){
        $lang = app()->getLocale();
        return $this->attributes["title_$lang"];
    }

    public function getHaveFreeContentAttribute(){
        $lectures = CourseLecture::where(['category_id' => $this->id, 'is_free_content' => 1])->first();
    }

    public function subs(){
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function parent(){
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function ScopeFilter($query)
    {
        $query->when(request()->has('q'), function ($quer) {
            $quer->where(function ($query) {
                $query->where('title_en', 'LIKE', '%'.request()->q.'%')
                    ->orWhere('title_ar', 'LIKE', '%'.request()->q.'%');
            });
        });
    }

    public function ScopeSorter($query){
        $query->when(request()->has('sortBy'), function ($quer) {
            $sortByDir = request()->get('sortDesc') == 'true' ? "desc" : "asc";
            switch (request()->get('sortBy')) {
                case 'title_en':
                    $quer->orderBy('title_en', $sortByDir);
                    break;
                case 'title_ar':
                    $quer->orderBy('title_ar', $sortByDir);
                    break;
                default:
                    $quer->orderBy('id', $sortByDir);
            }
        });
    }
}
