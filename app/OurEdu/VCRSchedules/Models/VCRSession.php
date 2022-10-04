<?php

namespace App\OurEdu\VCRSchedules\Models;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Courses\Models\SubModels\CourseSession;
use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Quizzes\Quiz;
use App\OurEdu\SchoolAccounts\Classroom;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\ClassroomClassSession;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\BaseApp\Traits\Ratingable;
use App\OurEdu\Courses\Models\SubModels\LiveSession;
use App\OurEdu\Quizzes\Enums\QuizTimesEnum;
use App\OurEdu\Quizzes\Enums\QuizTypesEnum;
use App\OurEdu\Users\User;
use App\OurEdu\VCRSchedules\Observers\CreateVCRSessionObserver;
use App\OurEdu\VCRSessions\Models\RecordedVcrSession;
use App\OurEdu\VCRSessions\Models\VcrFinishLog;
use App\OurEdu\VCRSessions\Models\VCRSessionMedia;
use App\OurEdu\VCRSessions\Models\ZoomHost;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class VCRSession extends BaseModel
{
    // use SoftDeletes;
    use Ratingable,SoftDeletes, HasFactory;

    protected $table = 'vcr_sessions';

    protected $fillable = [
        'student_id',
        'instructor_id',
        'subject_id',
        'vcr_request_id',
        'price',
        'status',
        'session_id',
        'room_uuid',
        'agora_instructor_uuid',
        'agora_student_uuid', // for requested vcr session
        'vcr_session_type',
        'course_id',
        'course_session_id',
        'vcr_schedule_day_id',

        'time_to_start',
        'time_to_end',
        'vcr_schedule_day_id',
        'started_at',
        'ended_at',
        'classroom_id',
        'classroom_session_id',
        'subject_name',
        'is_notified',
        'is_ended_by_instructor',
        'instructor_end_time',
        'is_done_record'
    ];

    protected static function boot()
    {
        parent::boot();
        VCRSession::observe(CreateVCRSessionObserver::class);
    }

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
        return $this->belongsTo(VCRScheduleDays::class, 'vcr_schedule_day_id');
    }

    public function vcrSchedule()
    {
        return $this->hasMany(VCRSchedule::class, 'vcr_schedule_id');
    }

    public function courseSession()
    {
        return $this->belongsTo(CourseSession::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function liveSession()
    {
        return $this->belongsTo(LiveSession::class,'course_id');
    }

    public function participants()
    {
        return $this->hasMany(VCRSessionParticipant::class, 'vcr_session_id');
    }

    public function media()
    {
        return $this->hasMany(VCRSessionMedia::class, 'vcr_session_id');
    }

    public function recordedFile()
    {
        return $this->hasMany(RecordedVcrSession::class, 'vcr_session_id');
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class, 'vcr_session_id');
    }
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }
    public function classroomClassSession()
    {
        return $this->belongsTo(ClassroomClassSession::class, 'classroom_session_id');
    }

    public function sessionQuiz($quiz_time)
    {
        return $this->quizzes()->where('quiz_time', $quiz_time)->first();
    }

    public function beforeSessionQuizzes()
    {
        return $this->quizzes()
            ->where("quiz_time", "=", QuizTimesEnum::PRE_SESSION)
            ->where("quiz_type", "=", QuizTypesEnum::QUIZ)
            ->whereNotNull("published_at");
    }

    public function afterSessionQuizzes()
    {
        return $this->quizzes()
            ->where("quiz_time", "=", QuizTimesEnum::AFTER_SESSION)
            ->where("quiz_type", "=", QuizTypesEnum::QUIZ)
            ->whereNotNull("published_at");
    }

    public function finishLog()
    {
        return $this->hasMany(VcrFinishLog::class,'vcr_session_id');
    }

    public function VCRSessionPresence()
    {
        return $this->hasMany(VCRSessionPresence::class, "vcr_session_id");
    }

    public function VCRScheduleDays()
    {
        return $this->belongsTo(VCRScheduleDays::class, "vcr_schedule_day_id");
    }

    public function zoomHost(): BelongsTo
    {
        return $this->belongsTo(ZoomHost::class, "zoom_host_id");
    }

    public function exam()
    {
        return $this->hasOne(Exam::class, "vcr_session_id");
    }

    public function vcrRequest(): BelongsTo
    {
        return $this->belongsTo(VCRRequest::class, "vcr_request_id");
    }
}
