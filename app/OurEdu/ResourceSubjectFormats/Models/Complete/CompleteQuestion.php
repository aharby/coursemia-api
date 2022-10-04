<?php

namespace App\OurEdu\ResourceSubjectFormats\Models\Complete;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Exams\Models\PrepareExamQuestion;
use App\OurEdu\GeneralExams\Models\PreparedGeneralExamQuestion;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\OurEdu\ResourceSubjectFormats\Observers\Complete\CompleteQuestionObserver;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizQuestionBank;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudentAnswer;
use App\OurEdu\ResourceSubjectFormats\Models\QuestionHeadInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompleteQuestion extends BaseModel implements QuestionHeadInterface
{
    use SoftDeletes, HasFactory;

    protected $table = 'res_complete_questions';

    protected $fillable = [
        'question',
        'res_complete_data_id',
        'question_feedback',
        'time_to_solve',
        'model'
    ];

    public function acceptedAnswers()
    {
        return $this->hasMany(CompleteAcceptedAnswer::class, 'res_complete_question_id');
    }

    public function options()
    {
        return $this->hasMany(CompleteAcceptedAnswer::class, 'res_complete_question_id');
    }

    public function answer()
    {
        return $this->hasOne(CompleteAnswer::class, 'res_complete_question_id');
    }


    public function parentData()
    {
        return $this->belongsTo(CompleteData::class, 'res_complete_data_id');
    }

    protected static function boot()
    {
        parent::boot();
        CompleteQuestion::observe(CompleteQuestionObserver::class);
    }

     /**
     * @return mixed
     */
    public function questionHead()
    {
        return $this->parentData;
    }

    public function questionBank()
    {
        return $this->morphOne(GeneralQuizQuestionBank::class, "question");
    }

    public function generalQuizStudentAnswers()
    {
        return $this->morphMany(GeneralQuizStudentAnswer::class, "single_question");
    }

    public function prepareExamQuestion()
    {
        return $this->morphOne(PrepareExamQuestion::class,'question_table');
    }

    public function preparedGeneralExamQuestion()
    {
        return $this->morphOne(PreparedGeneralExamQuestion::class,'questionable');
    }

}
