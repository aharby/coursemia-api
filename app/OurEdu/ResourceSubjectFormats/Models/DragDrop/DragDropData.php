<?php

namespace App\OurEdu\ResourceSubjectFormats\Models\DragDrop;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Exams\Models\PrepareExamQuestion;
use App\OurEdu\GeneralExams\Models\PreparedGeneralExamQuestion;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizQuestionBank;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudentAnswer;
use App\OurEdu\Options\Option;
use App\OurEdu\ResourceSubjectFormats\Models\QuestionHeadInterface;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use App\OurEdu\ResourceSubjectFormats\Observers\DragDrop\DragDropDataObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DragDropData extends BaseModel implements QuestionHeadInterface
{
    use HasFactory;
    protected $table = 'res_drag_drop_data';

    protected $fillable = [
        'description',
        'resource_subject_format_subject_id',
        'drag_drop_type',
        'time_to_solve',
        'question_feedback',
        'model'
    ];

    public function resourceSubjectFormatSubject()
    {
        return $this->belongsTo(ResourceSubjectFormatSubject::class, 'resource_subject_format_subject_id');
    }

    public function questions()
    {
        return $this->hasMany(DragDropQuestion::class, 'res_drag_drop_data_id');
    }

    public function options()
    {
        return $this->hasMany(DragDropOption::class, 'res_drag_drop_data_id');
    }

    public function dragDropType()
    {
        return $this->belongsTo(Option::class, 'drag_drop_type');
    }

    protected static function boot()
    {
        parent::boot();
        DragDropData::observe(DragDropDataObserver::class);

        static::deleting(function (self $dragDropData) {
            $questions = $dragDropData->questions()->get();
            foreach ($questions as $question) {
                $question->delete();
            }

            $options = $dragDropData->options()->get();
            foreach ($options as $option) {
                $option->delete();
            }

            $questionBank = $dragDropData->questionBank()->first();
            if ($questionBank) {
                $questionBank->delete();
            }

            $prepareExamQuestion = $dragDropData->prepareExamQuestion()->first();
            if ($prepareExamQuestion) {
                $prepareExamQuestion->delete();
            }
        });
    }

    public function questionBank()
    {
        return $this->morphOne(GeneralQuizQuestionBank::class, "question");
    }

    /**
     * @return $this
     */
    public function questionHead()
    {
        return $this;
    }

    public function generalQuizStudentAnswers()
    {
        return $this->morphMany(GeneralQuizStudentAnswer::class, "single_question");
    }

    public function prepareExamQuestion()
    {
        return $this->morphOne(PrepareExamQuestion::class,'question_table');
    }
}
