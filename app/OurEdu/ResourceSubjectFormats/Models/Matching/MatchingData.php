<?php

namespace App\OurEdu\ResourceSubjectFormats\Models\Matching;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Exams\Models\PrepareExamQuestion;
use App\OurEdu\GeneralExams\Models\PreparedGeneralExamQuestion;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use App\OurEdu\ResourceSubjectFormats\Observers\Matching\MatchingDataObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MatchingData extends BaseModel
{
    use HasFactory;

    protected $table = 'res_matching_data';

    protected $fillable = [
        'description',
        'resource_subject_format_subject_id',
        'time_to_solve',
        'question_feedback',
    ];

    public function resourceSubjectFormatSubject()
    {
        return $this->belongsTo(ResourceSubjectFormatSubject::class, 'resource_subject_format_subject_id');
    }

    public function questions()
    {
        return $this->hasMany(MatchingQuestion::class, 'res_matching_data_id');
    }

    public function options()
    {
        return $this->hasMany(MatchingOption::class, 'res_matching_data_id');
    }

    public function prepareExamQuestion()
    {
        return $this->morphOne(PrepareExamQuestion::class,'question_table');
    }

    protected static function boot()
    {
        parent::boot();
        MatchingData::observe(MatchingDataObserver::class);

        static::deleting(function (self $matchingData) {
            $questions = $matchingData->questions()->get();
            foreach ($questions as $question) {
                $question->delete();
            }

            $options = $matchingData->options()->get();
            foreach ($options as $option) {
                $option->delete();
            }

            $prepareExamQuestion = $matchingData->prepareExamQuestion()->first();
            if ($prepareExamQuestion) {
                $prepareExamQuestion->delete();
            }
        });
    }

}
