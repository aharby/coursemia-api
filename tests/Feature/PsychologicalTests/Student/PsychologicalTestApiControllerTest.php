<?php

namespace Tests\Feature\PsychologicalTests\Student;

use Tests\TestCase;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Foundation\Testing\WithFaker;
use App\OurEdu\PsychologicalTests\Models\PsychologicalTest;
use App\OurEdu\PsychologicalTests\Models\SubModels\PsychologicalOption;
use App\OurEdu\PsychologicalTests\Models\SubModels\PsychologicalQuestion;
use App\OurEdu\PsychologicalTests\Models\SubModels\PsychologicalRecomendation;

class PsychologicalTestApiControllerTest extends TestCase
{
    use WithFaker;

    public function test_listing_available_psychological_tests()
    {
        $this->disableExceptionHandling();

        dump('test_listing_available_psychological_tests');

        create(PsychologicalTest::class);

        $user = create(User::class, ['type' => UserEnums::STUDENT_TYPE]);


        $response = $this->getJson(route('api.student.psychological_tests.get.index', ['language' => 'en']), $this->loginUsingHeader($user))
            ->assertOk()
            ->assertJsonStructure([
                "data" => [
                    [
                        'type',
                        'id',
                        'attributes' => [
                            'name',
                            'is_active'
                        ]
                    ]
                ]
            ]);
    }

    public function test_user_can_view_psycholgoical_test()
    {
        $this->disableExceptionHandling();

        dump('test_user_can_view_psycholgoical_test');

        $user = create(User::class, ['type' => UserEnums::STUDENT_TYPE]);

        $test = create(PsychologicalTest::class);


        $response = $this->getJson(route('api.student.psychological_tests.get.view', ['language' => 'en', 'id' => $test->id]), $this->loginUsingHeader($user))
            ->assertOk()
            ->assertJsonStructure([
                "data" => [
                        'type',
                        'id',
                        'attributes' => [
                            'name',
                            'is_active'
                        ]
                ]
            ]);
    }

    public function test_user_can_view_psycholgoical_test_questions()
    {
        $this->disableExceptionHandling();

        dump('test_user_can_view_psycholgoical_test_questions');

        $user = create(User::class, ['type' => UserEnums::STUDENT_TYPE]);

        $test = create(PsychologicalTest::class);

        create(PsychologicalQuestion::class, ['psychological_test_id' => $test->id]);


        $response = $this->getJson(route('api.student.psychological_tests.get.questions', ['language' => 'en', 'id' => $test->id]), $this->loginUsingHeader($user))
            ->assertOk()
            ->assertJsonStructure([
                "data" => [
                    [
                        'type',
                        'id',
                        'attributes' => [
                            'name',
                            'is_active'
                        ]
                    ]
                ]
            ]);
    }

    public function test_user_can_start_psycholgoical_test()
    {
        $this->disableExceptionHandling();

        dump('test_user_can_start_psycholgoical_test');

        $user = create(User::class, ['type' => UserEnums::STUDENT_TYPE]);

        $test = create(PsychologicalTest::class);

        create(PsychologicalQuestion::class, ['psychological_test_id' => $test->id], 4);
        create(PsychologicalOption::class, ['psychological_test_id' => $test->id], 4);


        $response = $this->postJson(route('api.student.psychological_tests.post.start', ['language' => 'en', 'id' => $test->id]), [], $this->loginUsingHeader($user))
            ->assertOk()
            ->assertJsonFragment(['message' => trans('api.Test started successfully')])
            ->assertJsonStructure([
                "data" => [
                    [
                        'type',
                        'id',
                        'attributes' => [
                            'name',
                            'is_active'
                        ]
                    ]
                ]
            ]);
    }


    public function test_user_can_answer_psycholgical_test()
    {
        dump('test_user_can_answer_psycholgical_test');

        $this->disableExceptionHandling();

        $user = create(User::class, ['type' => UserEnums::STUDENT_TYPE]);

        $test = create(PsychologicalTest::class);

        $question = create(PsychologicalQuestion::class, ['psychological_test_id' => $test->id, 'is_active' => true]);
        $option = create(PsychologicalOption::class, ['psychological_test_id' => $test->id, 'is_active' => true]);

        $request_data = [
            'data'  =>  [
                'type'  =>  'psychological_answer',
                'id'    =>  null,
                'attributes'    =>  [
                    'question_id' =>  $question->id,
                    'option_id' =>  $option->id,
                ]
            ]
        ];

        $response = $this->postJson(route('api.student.psychological_tests.post.answerQuestion', ['language' => 'en', 'id' => $test->id]), $request_data, $this->loginUsingHeader($user))
                ->assertStatus(200)
                ->assertJsonFragment(['message' => trans('api.Answered successfully')])
                ->assertJsonStructure([
                    "data" => [
                        [
                            'type',
                            'id',
                            'attributes' => [
                                'endpoint_url',
                            ]
                        ]
                    ]
                ]);

        $this->assertCount(1, $test->answers);
    }

    public function test_user_can_finish_psycholgoical_test()
    {
        $this->disableExceptionHandling();

        dump('test_user_can_finish_psycholgoical_test');

        $user = create(User::class, ['type' => UserEnums::STUDENT_TYPE]);

        $test = create(PsychologicalTest::class);

        $question = create(PsychologicalQuestion::class, ['psychological_test_id' => $test->id]);
        $option = create(PsychologicalOption::class, ['psychological_test_id' => $test->id]);
        $option = create(PsychologicalRecomendation::class, ['psychological_test_id' => $test->id, 'from' => 0, 'to' => 100]);

        $test->answers()->create([
            'psychological_question_id' =>  $question->id,
            'psychological_option_id' =>  $option->id,
            'user_id' =>  $user->id,
        ]);


        $response = $this->postJson(route('api.student.psychological_tests.post.finish', ['language' => 'en', 'id' => $test->id]), [], $this->loginUsingHeader($user))
            ->assertOk()
            ->assertJsonFragment(['message' => trans('api.Finished successfully')])
            ->assertJsonStructure([
                "data" => [
                        'type',
                        'id',
                        'attributes' => [
                            'percentage',
                            
                        ]
                    ]
            ]);

        $this->assertCount(1, $test->results);
    }
}
