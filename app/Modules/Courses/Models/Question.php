<?php

namespace App\Modules\Courses\Models;

use App\Modules\Users\Admin\Models\Admin;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory, Translatable;

    protected $guarded = [];
    protected $table = "questions";

    protected $fillable = [
        'course_id',
        'admin_id',
        'image',
        'is_active',
        'explanation_image',
        'explanation_voice',
    ];

    protected $translationForeignKey = "question_id";
    protected $translatedAttributes = [
        'title',
        'description',
        'explanation',
    ];

    public function ScopeActive($query)
    {

        $query->where('is_active', '=', 1);
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }
    public function course(){
        return $this->belongsTo(Course::class);
    }

    public function getTranslatedTitleAttribute()
    {
        return $this->translate(app()->getLocale())->title;
    }
    public function getTranslatedDescriptionAttribute()
    {
        return $this->translate(app()->getLocale())->description;
    }
    public function getTranslatedExplanationAttribute()
    {
        return $this->translate(app()->getLocale())->explanation;
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function ScopeFilter($query)
    {
        $query->when(request()->has('is_active'), function ($quer) {
            $quer->where(function ($q) {
                $q->where('is_active', request()->boolean('is_active'));
            });
        })
            ->when(request()->get('q') != '', function ($query) {
                $query->where(function ($q) {
                    $q->orWhereTranslationLike('title', '%' . request()->get('q') . '%');
                });
            })
            ->when(request()->get('course') != '', function ($query) {
                $query->where(function ($q) {
                    $q->where('course_id', request()->course);
                });
            })
        ->when(request()->get('category') != '', function ($query) {
                $query->where(function ($q){
                    $q->where('category_id', request()->category)
                        ->orWhereHas('category', function ($cat){
                            $cat->whereHas('parent', function ($parent){
                                $parent->where('id', request()->category);
                            });
                        });
                });
            })
            ->when(request()->get('category_ids') != '', function ($query) {
                $query->where(function ($q){
                    $q->whereIn('category_id', request()->category_ids)
                        ->orWhereHas('category', function ($cat){
                            $cat->whereHas('parent', function ($parent){
                                $parent->where('id', request()->category_ids);
                            });
                        });
                });
            })
            ->when(
                request()->has('sub_category'),
                function ($quer) {
                    $quer->where('category_id', '=', request()->sub_category);
                }
            );
    }

    public function ScopeSorter($query)
    {
        $query->when(request()->has('sortBy'), function ($quer) {
            $sortByDir = request()->get('sortDesc') == 'true' ? "desc" : "asc";
            switch (request()->get('sortBy')) {
                case 'title_en':
                case 'title_ar':
                    $quer->orderByTranslation('title', $sortByDir);
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
}
