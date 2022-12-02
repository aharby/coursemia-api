<?php

namespace App\Modules\Courses\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseFlashcard extends Model
{
    use HasFactory, Translatable;


    protected $guarded = [];
    protected $table = "course_flashcards";

    protected $fillable = [
        'course_id',
        'category_id',
        'is_free_content',
    ];

    protected $translationForeignKey = "course_flashcards_id";
    protected $translatedAttributes = [
        'front',
        'back',
    ];


    public function ScopeActive($query)
    {
        $query->where('is_active', 1);
    }

    public function ScopeFilter($query)
    {
        $query->when(
            request()->has('is_active'),
            function ($quer) {
                $quer->where(
                    function ($q) {
                        $q->where('is_active', request()->boolean('is_active'));
                    }
                );
            }
        )
            ->when(
                request()->has('course'),
                function ($quer) {
                    $quer->where('course_id', '=', request()->course);
                }
            )
            ->when(
                request()->has('category'),
                function ($quer) {
                    $quer->where('category_id', '=', request()->category);
                }
            )->when(
                request()->has('sub_category'),
                function ($quer) {
                    $quer->where('category_id', '=', request()->sub_category);
                }
            );
    }

    public function ScopeSorter($query)
    {
        $query->when(
            request()->has('sortBy'),
            function ($quer) {
                $sortByDir = request()->get('sortDesc') == 'true' ? "desc" : "asc";
                switch (request()->get('sortBy')) {
                    case 'front_en':
                    case 'front_ar':
                        $quer->orderByTranslation('front', $sortByDir);
                        break;
                    case 'back_en':
                    case 'back_ar':
                        $quer->orderByTranslation('back', $sortByDir);
                        break;
                    default:
                        $quer->orderBy('id', $sortByDir);
                }
            }
        );
    }

    public function getFrontAttribute()
    {
        return $this->translate(app()->getLocale())->front;
    }

    public function getBackAttribute()
    {
        return $this->translate(app()->getLocale())->back;
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
