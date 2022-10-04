<?php

namespace App\Observers;

use App\OurEdu\Options\Enums\OptionsTypes;
use App\OurEdu\Options\Option;
use App\Producers\AcademicYear\AcademicYearCreated;
use App\Producers\AcademicYear\AcademicYearUpdated;
use App\Producers\EducationalTerm\EducationalTermCreated;
use App\Producers\EducationalTerm\EducationalTermUpdated;

class OptionObserver
{
    public function created(Option $option)
    {
        if ($option->type ==OptionsTypes::ACADEMIC_YEAR) {
            $payload = [
               'our_edu_reference' => null,
               'name_en' => $option->translate('en')->title,
               'name_ar' => $option->translate('ar')->title,
               'ta3lom_reference' => $option->id
            ];
            AcademicYearCreated::publish($payload);
        }
        if ($option->type ==OptionsTypes::EDUCATIONAL_TERM) {
            $payload = [
               'our_edu_reference' => null,
               'name_en' => $option->translate('en')->title,
               'name_ar' => $option->translate('ar')->title,
               'ta3lom_reference' => $option->id
            ];
            EducationalTermCreated::publish($payload);
        }
    }

    public function updated(Option $option)
    {
        if ($option->type ==OptionsTypes::ACADEMIC_YEAR) {
            $payload = [
                'our_edu_reference' => $option->our_edu_reference,
                'name_en' => $option->translate('en')->title,
                'name_ar' => $option->translate('ar')->title,
                'ta3lom_reference' => $option->id
            ];
            AcademicYearUpdated::publish($payload);
            if ($option->type ==OptionsTypes::EDUCATIONAL_TERM) {
                $payload = [
                    'our_edu_reference' => $option->our_edu_reference,
                    'name_en' => $option->translate('en')->title,
                    'name_ar' => $option->translate('ar')->title,
                    'ta3lom_reference' => $option->id
                ];
                EducationalTermUpdated::publish($payload);
            }
        }
    }
}
