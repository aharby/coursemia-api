<?php

namespace App\Modules\Countries\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory,Translatable;
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

        $query->where('is_active',1);
    }

    public function getTranslatedTitleAttribute()
    {
        return $this->translate(app()->getLocale())->title;
    }
}
