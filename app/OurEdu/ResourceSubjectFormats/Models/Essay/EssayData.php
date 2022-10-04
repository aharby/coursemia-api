<?php

namespace App\OurEdu\ResourceSubjectFormats\Models\Essay;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\BaseApp\Traits\CreatedBy;
use App\OurEdu\Countries\Country;
use App\OurEdu\EducationalSystems\EducationalSystem;
use App\OurEdu\Exams\Models\PrepareExamQuestion;
use App\OurEdu\GeneralExams\Models\PreparedGeneralExamQuestion;
use App\OurEdu\GradeClasses\GradeClass;
use App\OurEdu\Options\Option;
use App\OurEdu\ResourceSubjectFormats\Models\QuestionHeadInterface;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use App\OurEdu\ResourceSubjectFormats\Observers\TrueFalse\TrueFalseDataObserver;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use App\OurEdu\Subjects\Models\SubModels\SubjectMedia;
use App\OurEdu\Subjects\Models\SubModels\Task;
use App\OurEdu\Subjects\Observers\SubjectObserver;
use App\OurEdu\Users\User;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class EssayData extends BaseModel implements QuestionHeadInterface
{

    protected $table = 'res_essay_data';

    protected $fillable = [
        'description',
        'resource_subject_format_subject_id',
    ];

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        static::deleting(function (self $essayData) {
            $questions = $essayData->questions()->get();
            foreach ($questions as $question) {
                $question->delete();
            }

            $prepareExamQuestion = $essayData->prepareExamQuestion()->first();
            if ($prepareExamQuestion) {
                $prepareExamQuestion->delete();
            }
        });
    }

    public function resourceSubjectFormatSubject()
    {
        return $this->belongsTo(ResourceSubjectFormatSubject::class, 'resource_subject_format_subject_id');
    }

    public function questions()
    {
        return $this->hasMany(EssayQuestion::class, 'res_essay_data_id');
    }

    /**
     * @return $this
     */
    public function questionHead()
    {
        return $this;
    }

    public function questionBank()
    {
        return $this->questions()->first()->questionBank();
    }

    public function prepareExamQuestion()
    {
        return $this->morphOne(PrepareExamQuestion::class,'question_table');
    }
}
