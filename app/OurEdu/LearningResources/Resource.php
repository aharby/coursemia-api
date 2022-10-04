<?php

namespace App\OurEdu\LearningResources;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\BaseApp\Traits\CreatedBy;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resource extends BaseModel
{
    use SoftDeletes, CreatedBy, Translatable, HasFactory;

    protected $translatedAttributes = [
        'title',
        'description',
    ];

    protected $fillable = [
        'slug',
        'created_by',
    ];
}
