<?php

namespace App\OurEdu\Exams\Models;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use App\OurEdu\Users\Models\Student;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrepareExamQuestion extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'prepare_exam_questions';

    protected $fillable = [
        'difficulty_level',
        'question_type',
        'question_table_id',
        'question_table_type',
        'subject_id',
        'subject_format_subject_id',
        'time_to_solve',
        'is_done'
    ];

    public function question()
    {
        return $this->morphTo('question_table');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function subjectFormatSubject()
    {
        return $this->belongsTo(SubjectFormatSubject::class);
    }

    public function examStudents()
    {
        return $this->belongsToMany(
            Student::class,
            'prepare_exam_question_student',
            'prepare_exam_question_id',
            'student_id'
        );
    }

    public function competitionStudents()
    {
        return $this->belongsToMany(
            Student::class,
            'prepare_competition_question_student',
            'prepare_exam_question_id',
            'student_id'
        );
    }

    public function practiceStudents()
    {
        return $this->belongsToMany(
            Student::class,
            'prepare_practice_question_student',
            'prepare_exam_question_id',
            'student_id'
        );
    }
}
