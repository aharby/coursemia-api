<?php

namespace App\OurEdu\ResourceSubjectFormats\Observers;

use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;

class ResourceSubjectFormatSubjectObserver
{


    public function created(ResourceSubjectFormatSubject $resource)
    {

        $this->checkSectionHasData($resource);
    }

    public function updated(ResourceSubjectFormatSubject $resource)
    {

       $this->checkSectionHasData($resource);
    }

    public function deleted(ResourceSubjectFormatSubject $resource)
    {

       $this->checkSectionHasOtherData($resource);
    }


    public function checkSectionHasData(ResourceSubjectFormatSubject $resource) {
        if (in_array($resource->resource_slug , LearningResourcesEnums::getNotQuestionResources())) {
            $resource->subjectFormatSubject->update(['has_data_resources' => true]);
        }
    }

    public function checkSectionHasOtherData(ResourceSubjectFormatSubject $resource) {
        if (in_array($resource->resource_slug , LearningResourcesEnums::getNotQuestionResources())) {

            $section = $resource->subjectFormatSubject;

            $dataResourcesCount = $section->resourceSubjectFormatSubject()
                ->whereIn('resource_slug' , LearningResourcesEnums::getNotQuestionResources())
                ->count();

            if (!$dataResourcesCount){
                $resource->subjectFormatSubject->update(['has_data_resources' => false]);
            }
        }
    }
}

