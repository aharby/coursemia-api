<?php

namespace App\OurEdu\ResourceSubjectFormats\Models\HotSpot;

use App\OurEdu\BaseApp\BaseModel;

use App\OurEdu\Exams\Models\PrepareExamQuestion;
use App\OurEdu\GeneralExams\Models\PreparedGeneralExamQuestion;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;


class HotSpotData extends BaseModel
{

    use SoftDeletes, HasFactory;
    protected $table = 'res_hot_spot_data';

    protected $fillable = [
        'description',
        'resource_subject_format_subject_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function (self $hotSpotData) {
            $questions = $hotSpotData->questions()->get();
            foreach ($questions as $question) {
                $question->delete();
            }
        });
    }

    public function resourceSubjectFormatSubject()
    {
        return $this->belongsTo(ResourceSubjectFormatSubject::class, 'resource_subject_format_subject_id');
    }

    public function questions()
    {
        return $this->hasMany(HotSpotQuestion::class, 'res_hot_spot_data_id');
    }

    public function media()
    {
        return $this->hasOne(HotSpotMedia::class,'res_hot_spot_data_id');
    }
}
