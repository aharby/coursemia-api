<?php

namespace App\Modules\Users\Models;

use App\Modules\Users\User;
use App\Modules\BaseApp\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentTeacher extends BaseModel
{
    use HasFactory;

    protected $table = 'student_teachers';

    protected $fillable = [
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
