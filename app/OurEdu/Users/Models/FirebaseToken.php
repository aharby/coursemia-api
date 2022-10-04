<?php

namespace App\OurEdu\Users\Models;

use App\OurEdu\Users\User;
use App\OurEdu\BaseApp\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class FirebaseToken extends BaseModel
{
//    use SoftDeletes;


    protected $fillable = ['user_id', 'device_token', 'fingerprint', 'device_type', 'is_school_student'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
