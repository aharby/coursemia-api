<?php

namespace App\OurEdu\ResourceSubjectFormats\Models\Audio;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Exams\Models\PrepareExamQuestion;
use App\OurEdu\GeneralExams\Models\PreparedGeneralExamQuestion;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AudioData extends BaseModel
{
    use HasFactory;

    protected $table = 'res_audio_data';

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        static::deleting(function (self $audioData) {
            $mediaFiles = $audioData->media()->get();
            foreach ($mediaFiles as $file) {
                $file->delete();
            }

            $prepareExamQuestion = $audioData->prepareExamQuestion()->first();
            if ($prepareExamQuestion) {
                $prepareExamQuestion->delete();
            }
        });
    }

    protected $fillable = [
        'title',
        'description',
        'resource_subject_format_subject_id',
        'audio_type',
        'link',
    ];

    public function resourceSubjectFormatSubject()
    {
        return $this->belongsTo(ResourceSubjectFormatSubject::class, 'resource_subject_format_subject_id');
    }

    public function media()
    {
        return $this->hasMany(AudioDataMedia::class,'res_audio_data_id');
    }

    public function prepareExamQuestion()
    {
        return $this->morphOne(PrepareExamQuestion::class,'question_table');
    }

}
