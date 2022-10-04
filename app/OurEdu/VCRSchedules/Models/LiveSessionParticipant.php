<?php

namespace App\OurEdu\VCRSchedules\Models;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Courses\Models\SubModels\CourseSession;
use App\OurEdu\Users\Models\Student;

class LiveSessionParticipant extends BaseModel
{
    protected $table = 'live_session_participants';

    protected $fillable = [
        'student_id',
        'session_id',
        'agora_student_uuid',
        'agora_instructor_uuid',
        'room_uuid',
        'course_id',
        'course_type',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function session()
    {
        return $this->belongsTo(CourseSession::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

}
