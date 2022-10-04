<?php

namespace App\OurEdu\VCRSchedules\Models;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\VCRSchedules\Observers\VCRScheduleDaysObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VCRScheduleDays extends BaseModel
{
    use HasFactory;

    protected $table = 'vcr_schedule_instructor_days';

    protected $fillable = [
        'day',
        'vcr_schedule_instructor_id',
        'from_time',
        'to_time',
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        VCRScheduleDays::observe(VCRScheduleDaysObserver::class);
    }

    public function vcrSchedule()
    {
        return $this->belongsTo(VCRSchedule::class, 'vcr_schedule_instructor_id');
    }

    public function vcrSessions()
    {
        return $this->hasMany(VCRSession::class, 'vcr_schedule_day_id');
    }
}
