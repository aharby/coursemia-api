<?php


namespace App\OurEdu\Roles;


use App\OurEdu\BaseApp\BaseModel;

class RoleTranslation extends BaseModel
{
    public $timestamps = false;
    protected $table = 'role_translation';
    protected $fillable = [
        'title'
    ];
}
