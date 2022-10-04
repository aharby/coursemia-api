<?php

namespace App\OurEdu\GeneralQuizzes\Models;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\BaseApp\Traits\CreatedBy;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\OurEdu\Subjects\Models\Subject;
class GeneralQuizQuestionBank extends BaseModel
{


    use SoftDeletes , CreatedBy;
    protected $table = 'general_quiz_question_bank';

    protected $fillable = [
        'question_type',
        'question_id',
        'general_quiz_id',
        'school_account_branch_id',
        'school_account_id',
        'subject_format_subject_id',
        'subject_id',
        'grade',
        'slug',
        'created_by',
        'public_status'
    ];

    public static $questionsPerPage = 1;

    public function questions()
    {
        return $this->morphTo('question');
    }

    public function subject(){
        return $this->belongsTo(Subject::class,'subject_id');
    }


/*    public function generalQuiz()
    {
        return $this->belongsTo(GeneralQuiz::class, "general_quiz_id");
    }*/

    public function generalQuiz()
    {
        return $this->belongsToMany(GeneralQuiz::class,'general_quiz_question','question_id','general_quiz_id')->withPivot('added_from_bank');
    }

    public function studentAnswers()
    {
        return $this->hasMany(GeneralQuizStudentAnswer::class,'general_quiz_question_id');
    }

    public function sections()
    {
        return $this->belongsToMany(SubjectFormatSubject::class, "general_quiz_subject_format_subject", "general_quiz_id", "subject_format_subject_id");
    }

    public function section()
    {
        return $this->belongsTo(SubjectFormatSubject::class, "subject_format_subject_id");
    }


    public function groupStudentAnswersByQuestion()
    {
        return $this->studentAnswers()
            ->groupBy("general_quiz_question_id");
    }

    public function groupStudentAnswersByStudent()
    {
        return $this->studentAnswers()
            ->groupBy("student_id");
    }

}
