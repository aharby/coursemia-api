<?php


namespace App\OurEdu\Reports;


use App\OurEdu\BaseApp\BaseModel;

class ReportSubjectFormatSubject extends BaseModel
{
    protected $table = 'report_subject_format_subject';
    protected $fillable = [
        'section_id',
        'section_parent_id',
        'subject_id'
    ];

}
