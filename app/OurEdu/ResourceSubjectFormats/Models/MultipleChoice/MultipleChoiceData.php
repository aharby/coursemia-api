<?php

namespace App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Exams\Models\PrepareExamQuestion;
use App\OurEdu\GeneralExams\Models\PreparedGeneralExamQuestion;
use App\OurEdu\Options\Option;
use App\OurEdu\ResourceSubjectFormats\Models\QuestionHeadInterface;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MultipleChoiceData extends BaseModel implements QuestionHeadInterface
{
    use SoftDeletes, HasFactory;

    protected $table = 'res_multiple_choice_data';

    protected $fillable = [
        'description',
        'resource_subject_format_subject_id',
        'multiple_choice_type',
    ];

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function (self $multipleChoiceData) {
            $questions = $multipleChoiceData->questions()->get();
            foreach ($questions as $question) {
                $question->delete();
            }

            $options = $multipleChoiceData->options()->get();
            foreach ($options as $option) {
                $option->delete();
            }
        });
    }

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
        return $this->hasMany(MultipleChoiceQuestion::class, 'res_multiple_choice_data_id');
    }

    /**
     * @return HasMany
     */
    public function options()
    {
        return $this->hasMany(MultipleChoiceOption::class, 'res_multiple_choice_question_id');
    }

    public function multipleChoiceType()
    {
        return $this->belongsTo(Option::class, 'multiple_choice_type');
    }

    public function optionType()
    {
        return $this->multipleChoiceType();
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
}
