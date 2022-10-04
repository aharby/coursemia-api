<?php

namespace App\OurEdu\VCRSchedules\Models;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Courses\Models\SubModels\CourseSession;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\Models\Instructor;
use App\OurEdu\BaseApp\Traits\Ratingable;
use App\OurEdu\Users\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class VcrReminder extends BaseModel
{
    // use SoftDeletes;
    use Ratingable;
    protected $table = 'vcr_reminder';

    protected $fillable = [
        'user_id',
        'user_email',
        'user_role',
        'session_id',
        'session_type',
        'room_uuid',
        'user_uuid',
        'session_start_date_time',
        'session_end_date_time',
        'sent_first',
        'sent',



    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function student()
    {
        return $this->belongsTo(VCRSession::class,'session_id');
    }


}
