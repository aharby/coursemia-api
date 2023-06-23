<?php

namespace App;

use App\Modules\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFollow extends Model
{
    use HasFactory;

    public function follower(){
        return $this->belongsTo(User::class, 'follower_id');
    }
}
