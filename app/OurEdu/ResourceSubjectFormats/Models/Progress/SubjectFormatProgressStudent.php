<?php

namespace App\OurEdu\ResourceSubjectFormats\Models\Progress;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;

class SubjectFormatProgressStudent extends BaseModel
{
    protected $table = 'subject_format_progress_student';

    protected $fillable = [
        'student_id',
        'subject_id',
        'subject_format_id',
        'points',
        'is_visible'
    ];

    public function subjectFormatSubject(){
        return $this->belongsTo(SubjectFormatSubject::class,'subject_format_id');
    }
}
