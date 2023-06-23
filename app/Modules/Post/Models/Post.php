<?php

namespace App\Modules\Post\Models;

use App\Modules\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    public function likes(){
        return $this->hasMany(PostLike::class);
    }

    public function comments(){
        return $this->hasMany(PostComment::class);
    }

    public function hashtags(){
        return $this->hasMany(PostHashtag::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function getIsLikedAttribute(){
        $liked = PostLike::where([
            'post_id' => $this->attributes['id'],
            'type' => 'like',
            'user_id' => auth('api')->user()->id
        ])->first();
        if (isset($liked))
            return true;
        return false;
    }

    public function getIsLovedAttribute(){
        $liked = PostLike::where([
            'post_id' => $this->attributes['id'],
            'type' => 'love',
            'user_id' => auth('api')->user()->id
        ])->first();
        if (isset($liked))
            return true;
        return false;
    }
}
