<?php

namespace App\OurEdu\Subjects\Repository\StudentProgress;

use App\OurEdu\ResourceSubjectFormats\Models\Progress\SubjectFormatProgressStudent;

class SubjectFormatProgressStudentRepository implements SubjectFormatProgressStudentRepositoryInterface
{

    private $subjectFormatProgressStudent;

    public function __construct(SubjectFormatProgressStudent $subjectFormatProgressStudent)
    {
        $this->subjectFormatProgressStudent = $subjectFormatProgressStudent;
    }
    public function firstOrCreate($data){
     return   $this->subjectFormatProgressStudent->firstOrCreate($data);
    }

    public function incrementPoints($data,$points){
        $obj=$this->firstOrCreate($data);
        $obj->increment('points',$points);
        return $obj;
    }

    public function getTotalSubjectStudentPoint($studentId,$subjectId){

       return $this->subjectFormatProgressStudent
            ->with('subjectFormatSubject')
            ->whereHas('subjectFormatSubject',function ($q){
                $q->whereNull('parent_subject_format_id');
            })
            ->where('student_id',$studentId)
            ->where('subject_id',$subjectId)
            ->sum('points');


    }

}
