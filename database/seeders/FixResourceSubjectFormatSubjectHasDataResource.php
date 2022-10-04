<?php

namespace Database\Seeders;

use App\Exceptions\OurEduErrorException;
use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use Illuminate\Database\Seeder;

class FixResourceSubjectFormatSubjectHasDataResource extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $resources = ResourceSubjectFormatSubject::get();
        foreach ($resources as $resource) {

            if (in_array($resource->resource_slug, LearningResourcesEnums::getNotQuestionResources())) {
                try {
                    dump($resource->resource_slug);
                    $resource->subjectFormatSubject()->update(['has_data_resources' => true]);
                } catch (Throwable $e) {
                    throw new OurEduErrorException($e->getMessage());
                }
            }
        }
    }
}
