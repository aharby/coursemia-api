<?php


namespace App\OurEdu\Schools;


use App\OurEdu\BaseApp\BaseModel;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class SchoolRequests extends BaseModel implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'school_name',
        'number_of_students',
        'manager_name',
        'manager_mobile',
        'status',
        'manager_email'
    ];
    protected $translatedAttributes = [
        'name'
    ];
}
