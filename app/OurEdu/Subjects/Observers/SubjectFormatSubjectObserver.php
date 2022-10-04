<?php

namespace App\OurEdu\Subjects\Observers;

use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;

class SubjectFormatSubjectObserver
{

    public function updated(SubjectFormatSubject $subjectFormatSubject)
    {
        if ($subjectFormatSubject->has_data_resources) {
            if ($subjectFormatSubject->parentSubjectFormatSubject){
                $subjectFormatSubject->parentSubjectFormatSubject()->update(['has_data_resources' => true]);
            }
        } else {
            $this->checkSiblingsAndParent($subjectFormatSubject);
        }
    }


    public function deleted(SubjectFormatSubject $subjectFormatSubject)
    {
        if ($subjectFormatSubject->has_data_resources) {
            $this->checkSiblingsAndParent($subjectFormatSubject);
        }
    }

    public function checkSiblingsAndParent(SubjectFormatSubject $subjectFormatSubject) {
        $parent = $subjectFormatSubject->parentSubjectFormatSubject;
        if ($parent) {
            $sectionSiblingsWhichHaveDataResourcesCount = $parent->childSubjectFormatSubject()
                ->where('has_data_resources' , true)
                ->count();
            if (!$sectionSiblingsWhichHaveDataResourcesCount) {
                $parent->update(['has_data_resources' => false]);
            }
        }
    }

}
