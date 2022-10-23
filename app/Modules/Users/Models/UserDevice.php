<?php

namespace App\Modules\Users\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function getTypeAttribute(){
        if($this->attributes['device_type'] == 1)
            return 'Android';
        else
            return 'iOS';
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
