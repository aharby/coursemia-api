<?php

namespace App\Observers;

use App\OurEdu\EducationalSystems\EducationalSystem;
use App\OurEdu\GradeClasses\GradeClass;
use App\OurEdu\Subjects\Models\Subject;
use App\Producers\EducationalSystem\EducationalSystemCreated;
use App\Producers\EducationalSystem\EducationalSystemUpdated;
use App\Producers\Subject\SubjectCreated;
use App\Producers\Subject\SubjectUpdated;
use Illuminate\Support\Facades\Log;

class SubjectObserver
{
    public function created(Subject $subject)
    {        
    }



    public function updated(Subject $subject)
    {
    }
}
