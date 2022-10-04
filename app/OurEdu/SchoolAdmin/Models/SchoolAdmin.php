<?php

namespace App\OurEdu\SchoolAdmin\Models;

use App\OurEdu\SchoolAccounts\SchoolAccounts\SchoolAccount;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\OurEdu\Users\User;

class SchoolAdmin extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'current_school_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function currentSchool()
    {
        return $this->belongsTo(SchoolAccount::class,'current_school_id');
    }
    public function Schools()
    {
        return $this->belongsTo(SchoolAccount::class,'current_school_id');
    }
}