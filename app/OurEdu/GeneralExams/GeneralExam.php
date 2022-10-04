<?php

namespace App\OurEdu\GeneralExams;

use App\OurEdu\GeneralExamReport\Models\GeneralExamReport;
use App\OurEdu\GeneralExams\Models\GeneralExamStudent;
use App\OurEdu\Options\Option;
use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\BaseApp\Traits\CreatedBy;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\Models\Student;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\OurEdu\GeneralExams\Models\GeneralExamQuestion;
use App\OurEdu\GeneralExams\Models\PreparedGeneralExamQuestion;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GeneralExam extends BaseModel
{
    use CreatedBy, SoftDeletes, HasFactory;

    protected $dates = ['published_at'];

    /**
     * @var array
     */
    protected $fillable = [
        'uuid',
        'name',
        'date' ,
        'difficulty_level_id' ,
        'start_time',
        'subject_id',
        'end_time',
        'subject_format_subjects',
        'is_active'
    ];

    public function difficultyLevel()
    {
        return $this->belongsTo(Option::class, 'difficulty_level_id');
    }

    public function preparedQuestions()
    {
        return $this->belongsToMany(PreparedGeneralExamQuestion::class, 'general_exam_prepared_questions_pivot', 'general_exam_id', 'prepared_general_exam_question_id');
    }

    public function questions()
    {
        return $this->hasMany(GeneralExamQuestion::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function report() {
        return $this->hasOne(GeneralExamReport::class , 'general_exam_id');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, GeneralExamStudent::class,'general_exam_id', 'student_id');
    }
}
