<?php

namespace App\Modules\Post\Models;

use App\Modules\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostComment extends Model
{
    use HasFactory;

    public function user(){
        return $this->belongsTo(User::class);
    }
}
