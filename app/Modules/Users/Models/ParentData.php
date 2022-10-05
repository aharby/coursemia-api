<?php

namespace App\Modules\Users\Models;

use App\Modules\BaseApp\BaseModel;
use App\Modules\Users\User;

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
