<?php

namespace App\OurEdu\Users\Models;

use Carbon\Carbon;
use App\OurEdu\Users\User;
use App\OurEdu\Schools\School;
use App\OurEdu\BaseApp\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSchedules\Models\VCRSchedule;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Instructor extends BaseModel
{
    use SoftDeletes, HasFactory;

    protected $table = 'instructors';

    protected $fillable = [
        'about_instructor',
        'hire_date',
        'school_id',
        'user_id'
    ];

    protected $appends = ['instructor_total_hours'];

    public function getInstructorTotalHoursAttribute()
    {
        $duration = 0;
        foreach ($this->vcrSessions as $vcrSession) {
            if ($vcrSession->ended_at) {
                $endedAt = Carbon::parse($vcrSession->ended_at);
                $startedAt = Carbon::parse($vcrSession->created_at);
                $duration += $endedAt->diffInHours($startedAt);
            }
        }

        return $duration;
    }

    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vcrSessions()
    {
        return $this->hasMany(VCRSession::class);
    }

    public function vcrSchedule()
    {
        return $this->hasMany(VCRSchedule::class);
    }
}
