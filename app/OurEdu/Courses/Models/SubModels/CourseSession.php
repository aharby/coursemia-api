<?php

namespace App\OurEdu\Courses\Models\SubModels;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Courses\Models\Course;
use App\OurEdu\BaseApp\Traits\Ratingable;
use App\OurEdu\VCRSchedules\Models\LiveSessionParticipant;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class CourseSession extends BaseModel implements Auditable
{
    use Ratingable, HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'course_id',
        'status',
        'content',
        'date',
        'start_time',
        'end_time',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function liveSession()
    {
        return $this->belongsTo(LiveSession::class, 'course_id');
    }


    public function VCRSession()
    {
        return $this->hasOne(VCRSession::class, 'course_session_id');
    }

    public function getSessionStartTimeAttribute()
    {
        return "{$this->date} {$this->start_time}";
    }

    public function getSessionEndTimeAttribute()
    {
        return "{$this->date} {$this->end_time}";
    }
}
