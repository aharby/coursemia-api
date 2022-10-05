<?php


namespace App\Modules\Config;
use App\Modules\BaseApp\BaseModel;

class ConfigTranslation extends BaseModel
{
    public $timestamps = false;
    protected $table = 'config_translations';
    protected $fillable = [
        'label',
        'value'
    ];
}
