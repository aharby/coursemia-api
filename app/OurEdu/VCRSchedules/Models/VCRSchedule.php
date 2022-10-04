<?php

namespace App\OurEdu\VCRSchedules\Models;

use App\OurEdu\SchoolAccounts\Classroom;
use App\OurEdu\Users\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Arr;
use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\VCRSchedules\Observers\VCRScheduleObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class VCRSchedule extends BaseModel implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'vcr_schedule_instructor';

    protected $fillable = [
        'subject_id',
        'instructor_id',
        'from_date',
        'to_date',
        'is_active',
        'price',
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        // VCRSchedule::observe(VCRScheduleObserver::class);
    }

    public function scopeNextWeek($query)
    {
        $today = CarbonImmutable::now();
        $endOfNextWeek = $today->addWeek();

        return $query->where([
            ['from_date', '<', $endOfNextWeek],
            ['to_date', '>', $today],
        ]);
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function workingDays()
    {
        return $this->hasMany(VCRScheduleDays::class, 'vcr_schedule_instructor_id');
    }

    public function transformAudit(array $data): array
    {
        if (Arr::has($data, 'new_values.subject_id')) {
            if (is_null($this->getOriginal('subject_id'))) {
                $data['old_values']['subject_name'] = '';
            } else {
                $data['old_values']['subject_name'] = Subject::find($this->getOriginal('subject_id'))->name;
            }
            $data['new_values']['subject_name'] = Subject::find($this->getAttribute('subject_id'))->name;
        }
        if (Arr::has($data, 'new_values.instructor_id')) {
            if (is_null($this->getOriginal('instructor_id'))) {
                $data['old_values']['instructor_name'] = '';
            } else {
                $data['old_values']['instructor_name'] = User::find($this->getOriginal('instructor_id'))->name;
            }
            $data['new_values']['instructor_name'] = User::find($this->getAttribute('instructor_id'))->name;
        }
        return $data;
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function vcrSessions()
    {
        return $this->hasManyThrough(VCRSession::class, VCRScheduleDays::class, 'vcr_schedule_instructor_id', 'vcr_schedule_day_id');
    }
}
