<?php


namespace App\OurEdu\Config;
use App\OurEdu\BaseApp\BaseModel;

class ConfigTranslation extends BaseModel
{
    public $timestamps = false;
    protected $table = 'config_translations';
    protected $fillable = [
        'label',
        'value'
    ];
}
