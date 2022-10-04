<?php

namespace App\OurEdu\SchoolAccounts\ClassroomClassSessions\Instructor\Models;

use Illuminate\Database\Eloquent\Model;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\ClassroomClassSession;
use App\OurEdu\Users\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassroomClassSessionScores extends Model
{
    use SoftDeletes;
    protected $table = 'classroom_class_session_scores';

    protected $fillable = [
        'score_type',
        'student_id',
        'score',
        'classroom_id',
        'classroom_session_id',
    ];

    public function classroomClassSession()
    {
        return $this->belongsTo(ClassroomClassSession::class, 'classroom_session_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

}