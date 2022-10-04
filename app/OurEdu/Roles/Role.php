<?php


namespace App\OurEdu\Roles;


use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Users\UserEnums;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends BaseModel
{
    use SoftDeletes,
        Translatable;


    protected $table = "roles";

    protected $fillable = [ 'permissions' , 'school_account_id'];

    protected $hidden = [
        'deleted_at',
    ];
    protected $translatedAttributes = [
        'title'
    ];

    public function getPermissionsAttribute($value)
    {
        return json_decode($value);
    }

    public function setPermissionsAttribute($value)
    {
        if ($value) {
            $this->attributes['permissions'] = json_encode($value);
        }
    }

}
