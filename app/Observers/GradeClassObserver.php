<?php

namespace App\Observers;


use App\OurEdu\GradeClasses\GradeClass;
use App\Producers\GradeClass\GradeCreated;
use App\Producers\GradeClass\GradeUpdated;
use Illuminate\Support\Facades\Log;

class GradeClassObserver
{
    public function created(GradeClass $gradeClass)
    {
        if(is_null($gradeClass->our_edu_reference)){
            $payload = [
                'our_edu_reference' => null,
                'name_en' => $gradeClass->translate('en')->title,
                'name_ar' => $gradeClass->translate('ar')->title,
                'ta3lom_reference' => $gradeClass->id
            ];
            GradeCreated::publish($payload);    
        }
        Log::info('ss',['gg'=>$gradeClass]);
    }

    public function updated(GradeClass $gradeClass)
    {
        $payload = [
            'our_edu_reference' => $gradeClass->our_edu_reference,
            'name_en' => $gradeClass->translate('en')->title,
            'name_ar' => $gradeClass->translate('ar')->title,
            'ta3lom_reference' => $gradeClass->id
        ];
        GradeUpdated::publish($payload);
    }

}
