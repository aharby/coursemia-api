<?php

namespace App\Modules\Countries\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory, Translatable;

    protected $guarded = [];
    protected $table = "countries";

    protected $fillable = [
        'country_code',
        'flag',
        'is_active',
    ];

    protected $translationForeignKey = "country_id";
    protected $translatedAttributes = [
        'title',
    ];

    public function ScopeActive($query)
    {
        $query->where('is_active', 1);
    }

    public function ScopeFilter($query)
    {
        $query->when(request()->has('is_active'), function ($quer) {
            $quer->where(function ($q) {
                $q->where('is_active', request()->boolean('is_active'));
            });
        })
            ->when(request()->get('searchKey') != '', function ($query) {
                $query->where(function ($q) {
                    $q->where('country_code', 'like', '%' . request()->get('searchKey') . '%');
                    $q->orWhereTranslationLike('title', '%' . request()->get('searchKey') . '%');
                });
            });
    }

    public function ScopeSorter($query)
    {
        $query->when(request()->has('sortBy'), function ($quer) {
            $sortByDir = request()->get('sortDesc') ? "desc" : "asc";
            switch (request()->get('sortBy')) {
                case 'title_en':
                case 'title_ar':
                    $quer->orderByTranslation('title', $sortByDir);
                    break;
                case 'country_code':
                    $quer->orderBy('country_code', $sortByDir);
                    break;
                default:
                    $quer->orderBy('id', $sortByDir);
            }
        });
    }

    public function getTranslatedTitleAttribute()
    {
        return $this->translate(app()->getLocale())->title;
    }
}
