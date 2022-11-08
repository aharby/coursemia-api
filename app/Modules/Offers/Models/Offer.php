<?php

namespace App\Modules\Offers\Models;

use App\Modules\Courses\Models\Course;
use App\Modules\Courses\Models\OfferCourse;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory, Translatable;

    protected $guarded = [];
    protected $table = "offers";

    protected $fillable = [
        'image',
        'is_active',
        'expiration_date',
        'offer_value',
        'offer_type',
        'offer_code',
    ];

    protected $translationForeignKey = "offer_id";
    protected $translatedAttributes = [
        'title',
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

    public function getTranslatedTitleAttribute()
    {
        return $this->translate(app()->getLocale())->title;
    }

    public function courses()
    {
        return $this->hasManyThrough(Course::class, OfferCourse::class, 'offer_id', 'id', 'id', 'course_id');
    }

    public function offerCourses(){
        return $this->belongsToMany(Course::class, 'offer_courses');
    }
}
