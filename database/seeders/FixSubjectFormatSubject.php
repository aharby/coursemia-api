<?php

namespace Database\Seeders;

use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use Illuminate\Database\Seeder;

class FixSubjectFormatSubject extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $resources = ResourceSubjectFormatSubject::whereIn('resource_slug', LearningResourcesEnums::getNotQuestionResources())->get();

        foreach ($resources as $resource) {
            $subjectFormatSubject = $resource->subjectFormatSubject;
        }
    }

    private function checkIfSubjectFormatHasDataResource(SubjectFormatSubject $subjectFormatSubject)
    {
        if (!$subjectFormatSubject->has_data_resources) {
            $subjectFormatSubject->update(['has_data_resources' => true]);
        }
        if ($subjectFormatSubject->parentSubjectFormatSubject()->exists()) {
            $this->checkIfSubjectFormatHasDataResource($subjectFormatSubject->parentSubjectFormatSubject);
        }
    }
}
