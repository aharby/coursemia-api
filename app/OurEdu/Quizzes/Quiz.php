<?php

namespace App\OurEdu\Quizzes;

use App\OurEdu\BaseApp\Traits\CreatedBy;
use App\OurEdu\GradeClasses\GradeClass;
use App\OurEdu\Quizzes\Models\AllQuizStudent;
use App\OurEdu\Quizzes\Models\QuizQuestion;
use App\OurEdu\Quizzes\Models\QuizQuestionAnswer;
use App\OurEdu\Quizzes\Models\StudentQuiz;
use App\OurEdu\Quizzes\Observers\QuizObserver;
use App\OurEdu\SchoolAccounts\Classroom;
use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\ClassroomClassSession;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use function GuzzleHttp\Promise\all;

class Quiz extends BaseModel
{
    use CreatedBy;
    protected $table = 'quizzes';
    protected $fillable = [
        'quiz_type',
        'quiz_time',
        'creator_role',
        'created_by',
        'classroom_id',
        'subject_id',
        'classroom_class_id',
        'classroom_class_session_id',
        'vcr_session_id',
        'parent_quiz_id',
        'grade_class_id',
        'start_at',
        'end_at',
        'published_at',
        'subject_id',
        'quiz_title',
        'is_notified'
    ];


    protected static function boot()
    {
        parent::boot();
        Quiz::observe(QuizObserver::class);
    }
    public function getSuccessPercentageAttribute()
    {
        $successStudentsCount = $this->studentQuiz()->sum("quiz_result_percentage");
        $allStudentsCount = $this->studentQuiz()->count();
        $allStudentsCount = $allStudentsCount>0? $allStudentsCount : 1; // to make sure that {$allStudentsCount} all times contains value grater than zero

        return round($successStudentsCount / ($allStudentsCount), 2).'%';
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class, 'classroom_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function gradeClass()
    {
        return $this->belongsTo(GradeClass::class, 'grade_class_id');
    }

    public function classroomSession()
    {
        return $this->belongsTo(ClassroomClassSession::class, 'classroom_class_session_id');
    }

    public function VCRSession()
    {
        return $this->belongsTo(VCRSession::class, 'vcr_session_id');
    }

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class, 'quiz_id');
    }

    public function answers()
    {
        return $this->hasMany(QuizQuestionAnswer::class, 'quiz_id');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, "all_student_quiz",'quiz_id','student_id')
            ->withPivot(["quiz_type", "status", "quiz_result_percentage", "taken_at", "published_at", "subject_id"]);
    }

    public function parentQuiz()
    {
        return $this->belongsTo(Quiz::class, 'parent_quiz_id');
    }

    public function childQuiz()
    {
        return $this->hasOne(Quiz::class, 'parent_quiz_id');
    }

    public function allStudentQuiz()
    {
        return $this->hasMany(AllQuizStudent::class, 'quiz_id');
    }

    public function studentQuiz ()
    {
        return $this->hasMany(StudentQuiz::class);
    }

    public function branch()
    {
        return $this->belongsTo(SchoolAccountBranch::class, "branch_id");
    }
}
