<?php


namespace App\OurEdu\EducationalSystems;


use App\OurEdu\BaseApp\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;

class EducationalSystemTranslation extends BaseModel  implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public $timestamps = false;

    protected $fillable = [
        'name',
    ];
}
