<?php

namespace App\OurEdu\Exams\Models;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Exams\Models\Competitions\CompetitionQuestionStudent;
use App\OurEdu\Exams\Models\InstructorCompetitions\InstructorCompetitionQuestionStudent;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExamQuestion extends BaseModel
{
    use HasFactory;
    
    protected $table = 'exam_questions';

    protected $fillable = [
        'slug',
        'exam_id',
        'question_type',
        'question_table_type',
        'question_table_id',
        'subject_id',
        'subject_format_subject_id',
        'is_correct_answer',
        'time_to_solve',
        'is_answered',
        'student_time_to_solve',
    ];

    public static $questionsPerPage = 1;

    public function questionable()
    {
        return $this->morphTo('question_table');
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }


    public function answers()
    {
        return $this->hasMany(ExamQuestionAnswer::class, 'question_id');
    }

    public function examQuestionTimes()
    {
        return $this->hasMany(ExamQuestionTime::class, 'exam_question_id');
    }

    public function competitionQuestionStudents()
    {
        return $this->hasMany(CompetitionQuestionStudent::class);
    }

    public function instructorCompetitionQuestionStudents()
    {
        return $this->hasMany(InstructorCompetitionQuestionStudent::class);
    }
}
