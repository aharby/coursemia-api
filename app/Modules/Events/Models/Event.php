<?php

namespace App\Modules\Events\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory, Translatable;

    protected $guarded = [];
    protected $table = "events";

    protected $fillable = [
        'image',
        'is_active',
        'event_url'
    ];

    protected $translationForeignKey = "event_id";
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
                    $q->orWhereTranslationLike('title', '%' . request()->get('searchKey') . '%');
                });
            });
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

    public function getTranslatedTitleAttribute()
    {
        return $this->translate(app()->getLocale())->title;
    }
}
