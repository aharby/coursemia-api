<?php

namespace App\OurEdu\ResourceSubjectFormats\Models\TrueFalse;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Exams\Models\PrepareExamQuestion;
use App\OurEdu\GeneralExams\Models\PreparedGeneralExamQuestion;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizQuestionBank;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudentAnswer;
use App\OurEdu\ResourceSubjectFormats\Models\QuestionHeadInterface;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseQuestionMedia;
use App\OurEdu\ResourceSubjectFormats\Observers\TrueFalse\TrueFalseQuestionObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TrueFalseQuestion extends BaseModel implements QuestionHeadInterface
{
    use HasFactory;

    protected $table = 'res_true_false_questions';

    protected $fillable = [
        'text',
        'image',
        'is_true',
        'res_true_false_data_id',
        'time_to_solve',
        'question_feedback',
        'model',
        'audio_link',
        'video_link',
    ];

    public function prepareExamQuestion()
    {
        return $this->morphOne(PrepareExamQuestion::class,'question_table');
    }

    public function preparedGeneralExamQuestion()
    {
        return $this->morphOne(PreparedGeneralExamQuestion::class,'questionable');
    }

    public function options()
    {
        return $this->hasMany(TrueFalseOption::class, 'res_true_false_question_id');
    }

    public function parentData()
    {
        return $this->belongsTo(TrueFalseData::class,'res_true_false_data_id');
    }

    public function media()
    {
        return $this->hasOne(TrueFalseQuestionMedia::class,'res_true_false_question_id');
    }
    public function audio()
    {
        return $this->hasOne(TrueFalseQuestionAudio::class,'res_true_false_question_id');
    }
    public function video()
    {
        return $this->hasOne(TrueFalseQuestionVideo::class,'res_true_false_question_id');
    }
    protected static function boot()
    {
        parent::boot();
        TrueFalseQuestion::observe(TrueFalseQuestionObserver::class);
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
}
