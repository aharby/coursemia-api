<?php

namespace App\OurEdu\GeneralExamReport\Models;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Users\Models\ContentAuthor;
use Illuminate\Database\Eloquent\SoftDeletes;

class GeneralExamReportContentAuthorTask extends BaseModel
{
    use SoftDeletes;

    protected $table = 'general_exam_report_content_author_tasks';
    protected $fillable = [
        'task_id',
        'content_author_id',
        'created_at',
        'updated_at',

    ];

    public function contentAuthors()
    {
        return $this->belongsToMany(ContentAuthor::class, 'general_exam_report_content_author_tasks' , 'content_author_id')->withTimestamps();
    }

}
