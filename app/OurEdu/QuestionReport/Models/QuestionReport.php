<?php

namespace App\OurEdu\QuestionReport\Models;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\BaseApp\Traits\CreatedBy;
use App\OurEdu\Countries\Country;
use App\OurEdu\EducationalSystems\EducationalSystem;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use App\OurEdu\Users\Models\ContentAuthor;
use Astrotomic\Translatable\Translatable;
use App\OurEdu\Subjects\Models\Subject;
use Illuminate\Database\Eloquent\SoftDeletes;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Content;

class QuestionReport extends BaseModel
{
    use SoftDeletes;

    protected $table = 'question_reports';

    protected $fillable = [
        'slug',
        'subject_id',
        'subject_format_subject_id',
        'resource_subject_format_subject_id',
        'difficulty_level',
        'difficulty_level_result_equation',
        'question_type',
        'question_id',
        'total_answer',
        'correct_answer',
        'header',
        'is_ignored',
        'is_reported',
        'last_update'
    ];

    public function questionable()
    {
        return $this->morphTo('question');
    }

    public function scopeNotIgnored($query)
    {
        return $query->where('is_ignored', 0);
    }

    public function scopeNotReported($query)
    {
        return $query->where('is_reported', 0);
    }

    public function subjectFormatSubject()
    {
        return $this->belongsTo(SubjectFormatSubject::class, 'subject_format_subject_id');
    }

    public function scopeIgnored($query)
    {
        return $query->where('is_ignored', 1);
    }

    public function scopeReported($query)
    {
        return $query->where('is_reported', 1);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
    public function questionReportTask()
    {
        return $this->hasOne(QuestionReportTask::class, 'question_report_id');
    }
}
