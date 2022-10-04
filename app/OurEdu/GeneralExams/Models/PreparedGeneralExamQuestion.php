<?php

namespace App\OurEdu\GeneralExams\Models;

use App\OurEdu\Options\Option;
use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class PreparedGeneralExamQuestion extends BaseModel
{
    use HasFactory;
    use SoftDeletes;
    
    protected $fillable = [
        'questionable_type',
        'difficulty_level_id',
        'questionable_id',
        'subject_id',
        'subject_format_subject_id',
        'question_type',
    ];

    public static $questionsPerPage = 1;

    public function questionable()
    {
        return $this->morphTo();
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function subjectFormatSubject()
    {
        return $this->belongsTo(SubjectFormatSubject::class);
    }

    public function difficultyLevel()
    {
        return $this->belongsTo(Option::class, 'difficulty_level_id');
    }
}
