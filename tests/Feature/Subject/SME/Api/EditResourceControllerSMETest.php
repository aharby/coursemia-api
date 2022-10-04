<?php

namespace Tests\Feature\Subject\SME\Api;

use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use App\OurEdu\Subjects\Models\SubModels\ContentAuthorTask;
use App\OurEdu\Subjects\Models\SubModels\SubjectContentAuthor;
use Tests\TestCase;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\Models\ContentAuthor;
use Illuminate\Foundation\Testing\WithFaker;
use App\OurEdu\Subjects\Models\SubModels\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EditResourceControllerSMETest extends TestCase
{
    use WithFaker;

    public function test_edit_resource()
    {
        dump('test_edit_resource');
        $sme = $this->authSME();
        $this->apiSignIn($sme);
        $resources = LearningResourcesEnums::getQuestionLearningResources();

        foreach ($resources as $resource) {
            $resource = ResourceSubjectFormatSubject::where('resource_slug' , $resource)->get()->last();
            dump($resource->resource_slug . " " .$resource->id);
            $response = $this->get('api/v1/en/sme/subjects/edit-resource-subject-format/'.$resource->id);
            $response->assertOk();
            $responseBody = json_decode($response->content() , true);
            $response = $this->putJson('api/v1/en/sme/subjects/edit-resource-subject-format/'.$resource->id , $responseBody);
            $response->assertOk();

        }

        $resources = LearningResourcesEnums::getNotQuestionResources();

        foreach ($resources as $resource) {
            $resource = ResourceSubjectFormatSubject::where('resource_slug' , $resource)->first();
            dump($resource->resource_slug . " " .$resource->id);
            $response = $this->get('api/v1/en/sme/subjects/edit-resource-subject-format/'.$resource->id);
            $response->assertOk();
            $responseBody = json_decode($response->content() , true);
            $response = $this->putJson('api/v1/en/sme/subjects/edit-resource-subject-format/'.$resource->id , $responseBody);
            $response->assertOk();
        }



    }
}
