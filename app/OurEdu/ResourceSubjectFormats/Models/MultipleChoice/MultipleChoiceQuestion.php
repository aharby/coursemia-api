<?php

namespace App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice;

use App\OurEdu\BaseApp\BaseModel;

use App\OurEdu\GeneralQuizzes\Models\GeneralQuizQuestionBank;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudentAnswer;
use App\OurEdu\ResourceSubjectFormats\Models\QuestionHeadInterface;
use App\OurEdu\ResourceSubjectFormats\Observers\MultipleChoice\MultipleChoiceQuestionObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\OurEdu\Exams\Models\PrepareExamQuestion;
use App\OurEdu\GeneralExams\Models\PreparedGeneralExamQuestion;

class MultipleChoiceQuestion extends BaseModel implements QuestionHeadInterface
{
    use SoftDeletes, HasFactory;

    protected $table = 'res_multiple_choice_questions';

    protected $fillable = [
        'question',
        'url',
        'res_multiple_choice_data_id',
        'time_to_solve',
        'question_feedback',
        'model',
        'audio_link',
        'video_link',
    ];

    public function options()
    {
        return $this->hasMany(MultipleChoiceOption::class, 'res_multiple_choice_question_id');
    }

    public function media()
    {
        return $this->hasOne(MultipleChoiceQuestionMedia::class, 'res_multiple_choice_question_id');
    }

    public function audio()
    {
        return $this->hasOne(MultipleChoiceQuestionAudio::class, 'res_multiple_choice_question_id');
    }

    public function video()
    {
        return $this->hasOne(MultipleChoiceQuestionVideo::class, 'res_multiple_choice_question_id');
    }

    public function parentData()
    {
        return $this->belongsTo(MultipleChoiceData::class, 'res_multiple_choice_data_id');
    }

    protected static function boot()
    {
        parent::boot();
        MultipleChoiceQuestion::observe(MultipleChoiceQuestionObserver::class);
    }


    public function prepareExamQuestion()
    {
        return $this->morphOne(PrepareExamQuestion::class,'question_table');
    }

    public function preparedGeneralExamQuestion()
    {
        return $this->morphOne(PreparedGeneralExamQuestion::class,'questionable');
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
