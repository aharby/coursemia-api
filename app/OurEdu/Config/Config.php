<?php

namespace App\OurEdu\Config;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\BaseApp\Traits\CreatedBy;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Config extends BaseModel {
    use CreatedBy, Translatable, HasFactory;

    protected $table = "configs";

    protected $fillable = [
        'field_type',
        'field_class',
        'type',
        'field',
        'created_by'
    ];

    protected $translationForeignKey = "config_filed_id";
    protected $translatedAttributes = [
        'label',
        'value'
    ];

    public $rules = [
        'type' => 'required',
        'field' => 'required',
    ];

}
