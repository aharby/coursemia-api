<?php

namespace App\OurEdu\ResourceSubjectFormats\Models\DragDrop;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudentAnswer;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DragDropQuestion extends BaseModel
{
    use HasFactory;

    protected $table = 'res_drag_drop_questions';

    protected $fillable = [
        'question',
        'image',
        'res_drag_drop_data_id',
        'correct_option_id',
    ];


    public function correctOption()
    {
        return $this->belongsTo(DragDropOption::class, 'correct_option_id');
    }


    public function media()
    {
        return $this->hasOne(DragDropQuestionMedia::class, 'res_drag_drop_question_id');
    }

    public function audio()
    {
        return $this->hasOne(DragDropQuestionAudio::class, 'res_drag_drop_question_id');
    }

    public function video()
    {
        return $this->hasOne(DragDropQuestionVideo::class, 'res_drag_drop_question_id');
    }

    public function generalQuizStudentAnswers()
    {
        return $this->morphMany(GeneralQuizStudentAnswer::class, "single_question");
    }

    public function parentData()
    {
        return $this->belongsTo(DragDropData::class, 'res_drag_drop_data_id');
    }

    public function questionHead()
    {
        return $this->parentData;
    }

}
