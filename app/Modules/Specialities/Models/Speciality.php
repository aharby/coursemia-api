<?php

namespace App\Modules\Specialities\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Speciality extends Model
{
    use HasFactory,Translatable;
    protected $guarded = [];
    protected $table = "specialities";

    protected $fillable = [
        'image',
        'is_active',
    ];

    protected $translationForeignKey = "speciality_id";
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
