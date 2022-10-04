<?php

namespace App\OurEdu\ResourceSubjectFormats\Models\MultiMatching;

use App\OurEdu\BaseApp\BaseModel;

use App\OurEdu\Exams\Models\PrepareExamQuestion;
use App\OurEdu\GeneralExams\Models\PreparedGeneralExamQuestion;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use App\OurEdu\ResourceSubjectFormats\Observers\MultiMatching\MultiMatchingDataObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MultiMatchingData extends BaseModel
{
    use HasFactory;

    protected $table = 'res_multi_matching_data';

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
        return $this->hasMany(MultiMatchingQuestion::class, 'res_multi_matching_data_id');
    }
    public function options()
    {
        return $this->hasMany(MultiMatchingOption::class, 'res_multi_matching_data_id');
    }

    public function prepareExamQuestion()
    {
        return $this->morphOne(PrepareExamQuestion::class,'question_table');
    }

    protected static function boot()
    {
        parent::boot();
        MultiMatchingData::observe(MultiMatchingDataObserver::class);

        static::deleting(function (self $multiMatchingData) {
            $questions = $multiMatchingData->questions()->get();
            foreach ($questions as $question) {
                $question->delete();
            }

            $options = $multiMatchingData->options()->get();
            foreach ($options as $option) {
                $option->delete();
            }

            $prepareExamQuestion = $multiMatchingData->prepareExamQuestion()->first();
            if ($prepareExamQuestion) {
                $prepareExamQuestion->delete();
            }
        });
    }
}
