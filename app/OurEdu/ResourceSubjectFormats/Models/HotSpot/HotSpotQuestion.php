<?php

namespace App\OurEdu\ResourceSubjectFormats\Models\HotSpot;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Exams\Models\PrepareExamQuestion;
use App\OurEdu\GeneralExams\Models\PreparedGeneralExamQuestion;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\OurEdu\ResourceSubjectFormats\Observers\HotSpot\HotSpotQuestionObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HotSpotQuestion extends BaseModel
{
    use SoftDeletes, HasFactory;

    protected $table = 'res_hot_spot_questions';

    protected $fillable = [
        'question',
        'image_width',
        'question_feedback',
        'time_to_solve',
        'res_hot_spot_data_id',
    ];

    public function answers()
    {
        return $this->hasMany(HotSpotAnswer::class, 'res_hot_spot_question_id');
    }

    public function parentData()
    {
        return $this->belongsTo(HotSpotData::class, 'res_hot_spot_data_id');
    }

    protected static function boot()
    {
        parent::boot();
        HotSpotQuestion::observe(HotSpotQuestionObserver::class);
    }

    public function options()
    {
        return $this->hasMany(HotSpotAnswer::class, 'res_hot_spot_question_id');
    }

    public function media()
    {
        return $this->hasOne(HotSpotQuestionMedia::class, 'res_hot_spot_question_id');
    }

    public function audio()
    {
        return $this->hasOne(HotSpotQuestionAudio::class, 'res_hot_spot_question_id');
    }

    public function video()
    {
        return $this->hasOne(HotSpotQuestionVideo::class, 'res_hot_spot_question_id');
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
