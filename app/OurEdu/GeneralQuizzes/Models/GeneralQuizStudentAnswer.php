<?php

namespace App\OurEdu\GeneralQuizzes\Models;

use App\OurEdu\Options\Option;
use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\BaseApp\Traits\CreatedBy;
use App\OurEdu\Users\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;

class GeneralQuizStudentAnswer extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'student_id',
        'subject_format_subject_id',
        'option_type',
        'option_id',
        'single_question_id',
        'single_question_type',
        'general_quiz_id',
        'general_quiz_question_id',
        'answer_text',
        'is_correct',
        'time_to_solve',
        'is_reviewed',
        'score',
    ];



    public function generalQuiz()
    {
        return $this->belongsTo(GeneralQuiz::class, 'general_quiz_id');
    }

    public function details()
    {
        return $this->hasMany(GeneralQuizStudentAnswerDetails::class, 'main_answer_id');
    }

    public function questionBank()
    {
        return $this->belongsTo(GeneralQuizQuestionBank::class, 'general_quiz_question_id');
    }

    public function optionable()
    {
        return $this->morphTo('option');
    }

    public function questionable()
    {
        return $this->morphTo('single_question');
    }
    public function section()
    {
        return $this->belongsTo(SubjectFormatSubject::class, "subject_format_subject_id");
    }

    public function student()
    {
        return $this->belongsTo(User::class, "student_id");
    }



}
