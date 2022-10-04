<?php


namespace App\OurEdu\AcademicYears;


use App\OurEdu\BaseApp\BaseModel;

class AcademicYearTranslation extends BaseModel
{
    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

}
