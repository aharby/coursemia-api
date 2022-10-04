<?php

namespace App\Observers;

use App\OurEdu\EducationalSystems\EducationalSystem;
use App\Producers\EducationalSystem\EducationalSystemCreated;
use App\Producers\EducationalSystem\EducationalSystemUpdated;


class EducationalSystemObserver
{
    public function created(EducationalSystem $educationalSystem)
    {
        if (is_null($educationalSystem->our_edu_reference)) {

            $payload = [
                'our_edu_reference' => null,
                'name_en' => $educationalSystem->translate('en')->name,
                'name_ar' => $educationalSystem->translate('ar')->name,
                'ta3lom_reference' => $educationalSystem->id
            ];
            EducationalSystemCreated::publish($payload);
        }
    }

    public function updated(EducationalSystem $educationalSystem)
    {
        $payload = [
            'our_edu_reference' => $educationalSystem->our_edu_reference,
            'name_en' => $educationalSystem->translate('en')->name,
            'name_ar' => $educationalSystem->translate('ar')->name,
            'ta3lom_reference' => $educationalSystem->id
        ];
        EducationalSystemUpdated::publish($payload);
    }
}
