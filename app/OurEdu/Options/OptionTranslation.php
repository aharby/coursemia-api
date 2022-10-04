<?php


namespace App\OurEdu\Options;
use App\OurEdu\BaseApp\BaseModel;
class OptionTranslation extends BaseModel
{
    public $timestamps = false;
    protected $table = 'option_translations';
    protected $fillable = [
        'title'
    ];
}
