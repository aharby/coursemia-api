<?php

namespace App\OurEdu\VCRSessions\General\Models;

use App\OurEdu\BaseApp\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserZoom extends BaseModel
{

    public $timestamps = false;

    protected $table = 'users_zoom';
    protected $fillable = ['user_id','zoom_id'];

}
