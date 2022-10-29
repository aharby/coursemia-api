<?php

namespace App\Modules\Courses\Models;

use App\Modules\Specialities\Models\Speciality;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    public function images(){
        return $this->hasMany(CourseImage::class);
    }

    public function speciality(){
        return $this->belongsTo(Speciality::class);
    }

    public function getTitleAttribute(){
        $lang = app()->getLocale();
        return $this->attributes["title_$lang"];
    }

    public function getDescriptionAttribute(){
        $lang = app()->getLocale();
        return $this->attributes["description_$lang"];
    }

    public function lectures(){
        return $this->hasMany(CourseLecture::class);
    }

    public function lecturesFreeContent(){
        $result = $this->hasOne(CourseLecture::class)
            ->where('course_lectures.is_free_content', '=', 1);
        return $result;
    }

    public function notes(){
        return $this->hasMany(CourseNote::class);
    }

    public function questions(){
        return $this->hasMany(Question::class);
    }

    public function flashCards(){
        return $this->hasMany(CourseFlashcard::class);
    }

    public function reviews(){
        return $this->hasMany(CourseReview::class);
    }

    public function getRateAttribute(){
        $rates = CourseReview::where('course_id', $this->attributes['id'])->pluck('rate')->toArray();
        if (count($rates) > 0)
            return array_sum($rates) / count($rates);
        return 0;
    }

    public function ScopeSorter($query)
    {
        $query->when(request()->has('sortBy'), function ($quer) {
            $sortByDir = request()->get('sortDesc') == 'true' ? "desc" : "asc";
            switch (request()->get('sortBy')) {
                case 'title_en':
                    $quer->orderByTranslation('title', $sortByDir);
                    break;
                case 'lectures_count':
                    $quer->withCount('lectures')->orderBy('lectures_count', $sortByDir);
                    break;
                case 'notes_count':
                    $quer->withCount('notes')->orderBy('notes_count', $sortByDir);
                    break;
                case 'questions_count':
                    $quer->withCount('questions')->orderBy('questions_count', $sortByDir);
                    break;
                case 'price':
                    $quer->orderBy('price', $sortByDir);
                    break;
                default:
                    $quer->orderBy('id', $sortByDir);
            }
        });
    }
}
