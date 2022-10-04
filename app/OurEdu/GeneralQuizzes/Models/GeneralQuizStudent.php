<?php

namespace App\OurEdu\GeneralQuizzes\Models;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\User;

class GeneralQuizStudent extends BaseModel
{
    protected $table = 'general_quiz_students';
    /**
     * @var array
     */
    protected $fillable = [
        'student_id',
        'subject_id',
        'general_quiz_id',
        'is_finished',
        'finish_at',
        'score_percentage',
        'score',
        'start_at',
        'is_reviewed',
        'questions_order',
        'student_test_duration',
        'order',
        'show_result'
    ];

    protected $casts = [
        'questions_order' => 'array'
    ];

    protected $appends = ['student_order'];

    public function generalQuiz()
    {
        return $this->belongsTo(GeneralQuiz::class, 'general_quiz_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'student_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function getStudentOrderAttribute()
    {
           $number = $this->order;

            if(lang() == 'en'){

                $number = getEnglishOrdinalsuffix($this->order);
            }

          return  trans('general_quizzes.student_order',
            ['order' =>  $number,
            'students'=> $this->generalQuiz->students_count ?? 0]);

    }
}
