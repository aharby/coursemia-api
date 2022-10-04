<?php

namespace App\OurEdu\QuestionReport\Models;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use App\OurEdu\Users\Models\ContentAuthor;
use App\OurEdu\Subjects\Models\Subject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionReportTask extends BaseModel
{
    use SoftDeletes, HasFactory;

    protected $table = 'question_report_tasks';
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
        'question_report_id',
        'question_type',
        'question_id',
        'pulled_at',
        'is_paused',
        'resource_subject_format_subject_id',
        'subject_format_subject_id',
        'created_by'
    ];

    public function resourceSubjectFormatSubject()
    {
        return $this->belongsTo(ResourceSubjectFormatSubject::class, 'resource_subject_format_subject_id');
    }

    public function contentAuthors()
    {
        return $this->belongsToMany(ContentAuthor::class, 'question_report_content_author_tasks', 'task_id', 'content_author_id')->withTimestamps();
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function subjectFormatSubject()
    {
        return $this->belongsTo(SubjectFormatSubject::class);
    }

    public function questionReport()
    {
        return $this->belongsTo(QuestionReport::class, 'question_report_id');
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
