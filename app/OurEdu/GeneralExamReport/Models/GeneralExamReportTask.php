<?php

namespace App\OurEdu\GeneralExamReport\Models;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\BaseApp\Traits\CreatedBy;
use App\OurEdu\Countries\Country;
use App\OurEdu\EducationalSystems\EducationalSystem;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use App\OurEdu\Users\Models\ContentAuthor;
use Astrotomic\Translatable\Translatable;
use App\OurEdu\Subjects\Models\Subject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Content;

class GeneralExamReportTask extends BaseModel
{
    use SoftDeletes;

    protected $table = 'general_exam_report_tasks';
    protected $fillable = [
        'title',
        'note',
        'slug',
        'is_active',
        'is_expired',
        'is_done',
        'is_assigned',
        'due_date',
        'subject_id',
        'general_exam_report_question_id',
        'question_type',
        'question_id',
        'pulled_at',
        'is_paused',
        'subject_format_subject_id',
        'created_by'
    ];

    public function resourceSubjectFormatSubject()
    {
        return $this->belongsTo(ResourceSubjectFormatSubject::class, 'resource_subject_format_subject_id');
    }

    public function contentAuthors()
    {
        return $this->belongsToMany(ContentAuthor::class, 'general_exam_report_content_author_tasks', 'task_id', 'content_author_id')->withTimestamps();
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function subjectFormatSubject()
    {
        return $this->belongsTo(SubjectFormatSubject::class);
    }

    public function generalExamReportQuestion()
    {
        return $this->belongsTo(GeneralExamReportQuestion::class, 'general_exam_report_question_id');
    }

    public function questionable()
    {
        return $this->morphTo('question');
    }

    public function scopeNotPaused(Builder $query)
    {
        return $query->where('is_paused', false);
    }
}
