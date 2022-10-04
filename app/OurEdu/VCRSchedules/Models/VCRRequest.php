<?php

namespace App\OurEdu\VCRSchedules\Models;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class VCRRequest extends BaseModel
{
    use SoftDeletes, HasFactory;

    protected $table = 'vcr_requests';

    protected $fillable = [
        'student_id',
        'instructor_id',
        'subject_id',
        'vcr_schedule_id',
        'vcr_day_id',
        'accepted_at',
        'exam_id',
        'price',
        'status',
    ];

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function workingDay()
    {
        return $this->belongsTo(VCRScheduleDays::class, 'vcr_day_id');
    }

    public function vcrSchedule()
    {
        return $this->hasMany(VCRSchedule::class, 'vcr_schedule_id');
    }
    public function vcrSession(){
        return $this->hasOne(VCRSession::class,'vcr_request_id');
    }
}
