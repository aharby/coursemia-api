<?php


namespace App\OurEdu\SchoolAccounts\ClassroomClassSessions;


use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Quizzes\Enums\QuizTimesEnum;
use App\OurEdu\Quizzes\Enums\QuizTypesEnum;
use App\OurEdu\Quizzes\Quiz;
use App\OurEdu\SchoolAccounts\Classroom;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\Observers\ClassroomClassSessionObserver;
use App\OurEdu\SchoolAccounts\SessionPreparations\Models\SessionPreparation;
use App\OurEdu\SchoolAccounts\ClassroomClass\ClassroomClass;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\User;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassroomClassSession extends BaseModel
{

    use SoftDeletes;

    protected $table = 'classroom_class_sessions';
    protected $fillable = [
        'classroom_id',
        'classroom_class_id',
        'instructor_id',
        'subject_id',
        "from",
        "to",
    ];
    protected $dates = ['from', 'to'];
    protected $appends = ['from_date', 'from_time', 'to_date', 'to_time'];


    protected static function boot()
    {
        parent::boot();
        ClassroomClassSession::observe(ClassroomClassSessionObserver::class);
    }

    public function classRoomClass()
    {

        return $this->belongsTo(ClassroomClass::class, 'classroom_class_id');
    }

    public function classroom()
    {

        return $this->belongsTo(Classroom::class, 'classroom_id');
    }

    public function subject()
    {

        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function instructor()
    {

        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function vcrSession()
    {
        return $this->hasOne(VCRSession::class, 'classroom_session_id');
    }


    public function preparation(){
        return $this->hasOne(SessionPreparation::class,'classroom_session_id');
    }

    public function getFromTimeAttribute()
    {
        return Carbon::parse($this->from)->format('H:i');
    }
    public function getFromDateAttribute()
    {
        return Carbon::parse($this->from)->format('Y-m-d');
    }

    public function getToDateAttribute()
    {
        return Carbon::parse($this->to)->format('Y-m-d');
    }
    public function getToTimeAttribute()
    {
        return Carbon::parse($this->to)->format('H:i');
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class, 'classroom_class_session_id');
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
}
