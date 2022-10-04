<?php


namespace App\OurEdu\Countries;


use App\OurEdu\BaseApp\BaseModel;

class CountryTranslation extends BaseModel
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'currency'
    ];

}
