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
                request()->has('offer_type'),
                function ($quer) {
                    $quer->where(
                        function ($q) {
                            $q->where('offer_type', request()->get('offer_type'));
                        }
                    );
                }
            )
            ->when(
                request()->get('searchKey') != '',
                function ($query) {
                    $query->where(
                        function ($q) {
                            $q->orWhereTranslationLike('title', '%' . request()->get('searchKey') . '%');
                            $q->orWhere('expiration_date', 'like', '%' . request()->get('searchKey') . '%');
                            $q->orWhere('offer_value', 'like', '%' . request()->get('searchKey') . '%');
                            $q->orWhere('offer_code', 'like', '%' . request()->get('searchKey') . '%');
                        }
                    );
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
                    case 'title_en':
                    case 'title_ar':
                        $quer->orderByTranslation('title', $sortByDir);
                        break;
                    case 'expiration_date':
                        $quer->orderBy('expiration_date', $sortByDir);
                        break;
                    case 'offer_value':
                        $quer->orderBy('offer_value', $sortByDir);
                        break;
                    case 'offer_type':
                        $quer->orderBy('offer_type', $sortByDir);
                        break;
                    case 'offer_code':
                        $quer->orderBy('offer_code', $sortByDir);
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
