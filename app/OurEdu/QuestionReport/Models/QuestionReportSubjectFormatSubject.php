<?php


namespace App\OurEdu\QuestionReport\Models;


use App\OurEdu\BaseApp\BaseModel;

class QuestionReportSubjectFormatSubject extends BaseModel
{
    protected $table = 'question_report_subject_format_subject';

    protected $fillable = [
        'section_id',
        'section_parent_id',
        'subject_id'
    ];

}
