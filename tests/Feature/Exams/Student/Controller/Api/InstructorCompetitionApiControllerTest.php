<?php


namespace Tests\Feature\Exams\Student\Controller\Api;

use ExamsSeeder;
use Tests\TestCase;
use App\OurEdu\Users\User;
use App\OurEdu\Options\Option;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Exams\Enums\ExamTypes;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Exams\Models\ExamQuestion;
use App\OurEdu\LearningResources\Resource;
use App\OurEdu\Options\Enums\OptionsTypes;
use Illuminate\Foundation\Testing\WithFaker;
use App\OurEdu\Subjects\Models\SubModels\Task;
use App\OurEdu\Exams\Models\PrepareExamQuestion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\OurEdu\Subjects\Models\SubModels\ContentAuthorTask;
use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseData;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseOption;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseQuestion;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;

class InstructorCompetitionApiControllerTest extends TestCase
{
    use WithFaker;

    public function test_join_instructor_competition()
    {
        dump('test_join_instructor_competition');
        $instructor = $this->authInstructor();
        $student = $this->authStudent();

        $this->apiSignIn($student);
        $competition = factory(Exam::class)->create([
            'type' => ExamTypes::INSTRUCTOR_COMPETITION,
            'creator_id' => $instructor->id
        ]);

        $response = $this->getJson('api/v1/en/student/instructor-competitions/join-competition/'.$competition->id);
        if ($competition->is_started == 1) {
            $response->assertJsonFragment(['detail' => trans('api.The competition is already started')]);
        }else {
            $response->assertJsonFragment(['message' => trans('api.Joined successfully')]);
        }
    }
}
