<?php


namespace Tests\Feature\QuestionReport\SME\Api;

use App\OurEdu\Subjects\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class QuestionReportControllerTest extends TestCase
{
    use WithFaker;

    public function test_get_subject_lists()
    {
        dump('test_get_subject_lists');
        // getting the necessary data before starting the unit test
        $this->seed('GenerateExamSeeder');
        $this->seed('ExamsSeeder');
        $sme = $this->authSME();
        $this->apiSignIn($sme);



        $subject = Subject::whereHas('subjectFormatSubject')->whereHas('preparedQuestions')->whereHas('questionReport')->latest()->first();
        $subject->update(['sme_id' => $sme->id]);

        $response = $this->get("/api/v1/en/sme/questions-report/");

        $response->assertOk();
        $response->assertJson($response->decodeResponseJson());
        $response->assertJsonStructure([
            'data' => [
                '*' =>[
                    'type',
                    'attributes' => [
                        "name",
                        "no_of_questions",
                    ]
                ],
            ]
        ]);
    }

    public function test_get_questions()
    {
        dump('test_get_questions');
        $sme = $this->authSME();
        $this->apiSignIn($sme);



        $subject = Subject::whereHas('subjectFormatSubject')->whereHas('preparedQuestions')->whereHas('questionReport')->latest()->first();
        $subject->update(['sme_id' => $sme->id]);


        $response = $this->get("/api/v1/en/sme/questions-report/subject/".$subject->id);

        $response->assertOk();
        $response->assertJson($response->decodeResponseJson());
        $response->assertJsonStructure([
            'data' => [
                '*' =>[
                    'type',
                    'attributes' => [
                        'total_answer',
                        'correct_answer',
                        'slug',
                        'header',
                        'difficulty_level',
                        'difficulty_level_result_equation',
                        'subject_format_subject'
                    ]
                ],
            ]
        ]);
    }

    public function test_view_question()
    {
        dump('test_view_question');
        $sme = $this->authSME();
        $this->apiSignIn($sme);




        $subject = Subject::whereHas('subjectFormatSubject')->whereHas('questionReport')->whereHas('preparedQuestions')->with([
            'preparedQuestions', 'subjectFormatSubject'
        ])->latest()->first();
        $subject->update(['sme_id' => $sme->id]);



        $response = $this->get("/api/v1/en/sme/questions-report/question/".$subject->questionReport()->first()->id);

        $response->assertOk();
        $response->assertJson($response->decodeResponseJson());
        $response->assertJsonStructure([
            'data' => [
                    'type',
                    'attributes' => [
                        'total_answer',
                        'correct_answer',
                        'slug',
                        'header',
                        'difficulty_level',
                        'difficulty_level_result_equation',
                        'subject_format_subject'
                    ]
                ],
        ]);
    }
}
