<?php

namespace App\OurEdu\ResourceSubjectFormats\Models\Complete;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Exams\Models\PrepareExamQuestion;
use App\OurEdu\GeneralExams\Models\PreparedGeneralExamQuestion;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use App\OurEdu\ResourceSubjectFormats\Observers\Complete\CompleteDataObserver;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\OurEdu\ResourceSubjectFormats\Models\QuestionHeadInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompleteData extends BaseModel implements QuestionHeadInterface
{
    use SoftDeletes, HasFactory;

    protected $table = 'res_complete_question_data';

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function (self $completeData) {
            $questions = $completeData->questions()->get();
            foreach ($questions as $question) {
                $question->delete();
            }
        });
    }

    protected $fillable = [
        'description',
        'resource_subject_format_subject_id',
    ];

    /**
     * @return BelongsTo
     */
    public function resourceSubjectFormatSubject()
    {
        return $this->belongsTo(ResourceSubjectFormatSubject::class, 'resource_subject_format_subject_id');
    }

    /**
     * @return HasMany
     */
    public function questions()
    {
        return $this->hasMany(CompleteQuestion::class, 'res_complete_data_id');
    }

    public function questionHead()
    {
        return $this;
    }

    public function questionBank()
    {
        return $this->questions()->first()->questionBank();
    }
}
