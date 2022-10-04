<?php

namespace App\OurEdu\QuestionReport\Models;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\BaseApp\Traits\CreatedBy;
use App\OurEdu\Countries\Country;
use App\OurEdu\EducationalSystems\EducationalSystem;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use App\OurEdu\Users\Models\ContentAuthor;
use Astrotomic\Translatable\Translatable;
use App\OurEdu\Subjects\Models\Subject;
use Illuminate\Database\Eloquent\SoftDeletes;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Content;

class QuestionReportContentAuthorTask extends BaseModel
{
    use SoftDeletes;

    protected $table = 'question_report_content_author_tasks';
    protected $fillable = [
        'task_id',
        'content_author_id',
        'created_at',
        'updated_at',

    ];
    public function resourceSubjectFormatSubject()
    {
        return $this->belongsTo(ResourceSubjectFormatSubject::class, 'resource_subject_format_subject_id');
    }

    public function contentAuthors()
    {
        return $this->belongsToMany(ContentAuthor::class, 'question_report_content_author_tasks' , 'content_author_id')->withTimestamps();
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
        return $this->belongsTo(QuestionReport::class , 'question_report_id');
    }

    public function questionable()
    {
        return $this->morphTo('question');
    }
}
