<?php

namespace App\OurEdu\Subjects\Repository\StudentProgress;

use App\OurEdu\ResourceSubjectFormats\Models\Progress\ResourceProgressStudent;
use App\OurEdu\ResourceSubjectFormats\Models\Progress\SubjectFormatProgressStudent;

class ResourceProgressStudentRepository implements ResourceProgressStudentRepositoryInterface
{

    private $resourceProgressStudent;
    private $returnedArr;

    public function __construct(ResourceProgressStudent $resourceProgressStudent)
    {
        $this->resourceProgressStudent = $resourceProgressStudent;
        $this->returnedArr = [];
    }


    public function firstOrCreate($data){
        return $this->resourceProgressStudent->firstOrCreate($data);
    }

    public function incrementPoints($data,$points){
        $returnedArr['resourceProgressObj'] = $this->firstOrCreate($data);
        // if the student didnt view the resource before
        if ($returnedArr['resourceProgressObj']->points == 0){
            // view it for the first time
            $returnedArr['resourceProgressObj']->increment('points',$points);
            // flag: this resource viewed once
            $returnedArr['viewed'] = true;
        }
        return $returnedArr;
    }


}
