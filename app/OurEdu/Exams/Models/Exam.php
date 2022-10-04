<?php

namespace App\OurEdu\Exams\Models;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\User;
use App\OurEdu\VCRSchedules\Models\VCRRequest;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exam extends BaseModel
{
    use HasFactory,SoftDeletes;

    protected $table = 'exams';

    protected $fillable = [
        'title',
        'student_id',
        'creator_id',
        'questions_number',
        'difficulty_level',
        'subject_id',
        'subject_format_subject_id',
        'is_finished',
        'is_started',
        'finished_time',
        'start_time',
        'time_to_solve', //in seconds
        'student_time_to_solve', //in seconds
        'result',
        'type',
        'solving_speed_percentage',
        'vcr_session_id',
        'course_id'
    ];

    public function examQuestions()
    {
        return $this->hasMany(ExamQuestion::class, 'exam_id');
    }

    public function questions()
    {
        return $this->hasMany(ExamQuestion::class, 'exam_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function examQuestionsTimes()
    {
        return $this->hasMany(ExamQuestionTime::class, 'exam_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function competitionStudents()
    {
        return $this->belongsToMany(Student::class, 'competition_student')->withPivot(['result','rank','is_finished'])->withTimestamps();
    }

    public function instructorCompetitionStudents()
    {
        return $this->belongsToMany(Student::class, 'instructor_competition_student')->withTimestamps();
    }

    /**
     * Check if this exam in progress
     * @return [type] [description]
     */
    public function inProgress()
    {
        return (boolean) ($this->is_started && ! $this->is_finished);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function challenges() {

        return $this->hasMany(ExamChallenge::class , 'exam_id');
    }

    public function challenged() {

        return $this->hasMany(ExamChallenge::class , 'related_exam_id');
    }

    public function VCRRequest() {
        return $this->hasMany(VCRRequest::class , 'exam_id');
    }

    public function VCRSession()
    {
        return $this->belongsTo(VCRSession::class , 'vcr_session_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class , 'course_id');
    }

    public function competitionStudentsQuestions()
    {
        return $this->belongsToMany(Student::class, 'competition_question_student');
    }

    public function courseCompetitionStudents()
    {
        return $this->belongsToMany(Student::class, 'course_competitions_students');
    }


}
