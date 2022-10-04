<?php


namespace App\OurEdu\GeneralExamReport\Models;


use App\OurEdu\BaseApp\BaseModel;

class GeneralExamQuestionReportSubjectFormatSubject extends BaseModel
{
    protected $table = 'general_exam_question_report_subject_format_subject';

    protected $fillable = [
        'section_id',
        'section_parent_id',
        'subject_id'
    ];

}
