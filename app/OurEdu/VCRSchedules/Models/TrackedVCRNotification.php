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

class TrackedVCRNotification extends BaseModel
{
    protected $table = 'tracked_vcr_notifications';

    protected $fillable = [
        'user_id',
        'user_role',
        'vcr_session_id',
        'vcr_session_type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
