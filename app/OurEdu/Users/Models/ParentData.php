<?php

namespace App\OurEdu\Users\Models;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Users\User;

class ParentData extends BaseModel
{
    protected $fillable = [
        'user_id',
        'password',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
