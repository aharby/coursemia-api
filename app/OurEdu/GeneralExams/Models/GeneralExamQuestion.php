<?php

namespace App\OurEdu\GeneralExams\Models;

use App\OurEdu\GeneralExams\Models\GeneralExamOption;
use App\OurEdu\Options\Option;
use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\GeneralExams\GeneralExam;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GeneralExamQuestion extends BaseModel
{
    use HasFactory;

    /**
     * @var array
     */
    protected $fillable = [
        'question',
        'description',
        'difficulty_level_id' ,
        'subject_format_subject_id' ,
        'is_true',
        'general_exam_id',
        'question_type',
        'questionable_type',
        'questionable_id',
    ];

    public static $questionsPerPage = 1;

    public function difficultyLevel()
    {
        return $this->belongsTo(Option::class, 'difficulty_level_id');
    }

    public function exam()
    {
        return $this->belongsTo(GeneralExam::class, 'general_exam_id');
    }

    public function options()
    {
        return $this->hasMany(GeneralExamOption::class);
    }

    public function questions()
    {
        return $this->hasMany(GeneralExamQuestionQuestion::class);
    }

    public function studentAnswers()
    {
        return $this->hasMany(GeneralExamStudentAnswer::class);
    }

    public function multiMatchingOptions()
    {
        return $this->belongsToMany(GeneralExamOption::class , 'general_exam_questions_options' , 'general_exam_question_id' , 'general_exam_option_id');
    }

    public function questionable()
    {
        return $this->morphTo();
    }

    public function media()
    {
        return $this->hasOne(GeneralExamQuestionMedia::class, 'general_exam_question_id');
    }
}
