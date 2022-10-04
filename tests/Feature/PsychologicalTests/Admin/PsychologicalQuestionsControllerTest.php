<?php

namespace Tests\Feature\PsychologicalTests\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use App\OurEdu\PsychologicalTests\Models\SubModels\PsychologicalQuestion;

class PsychologicalQuestionsControllerTest extends TestCase
{
    use WithFaker;

    /**
     * A basic test example.
     *
     * @return void
     */

    public function test_list_psychological_questions()
    {
        dump('test_list_psychological_questions');
        $this->disableExceptionHandling();

        $this->authAdmin();

        $row = create(PsychologicalQuestion::class);

        $response = $this
            ->withSession(['locale' => 'en'])
            ->get(route('admin.psychological_questions.get.index', $row->psychological_test_id))
            ->assertStatus(200);
    }

    public function test_create_psychological_questions()
    {
        dump('test_create_psychological_questions');

        $this->authAdmin();
        
        $row = factory(PsychologicalQuestion::class)->make()->toArray();

        $data = [
            'name:en' => $row['name'],
            'name:ar' => $row['name'],
        ];

        $row = array_merge($row, $data);

        $response = $this
            ->post(route('admin.psychological_questions.post.create', $row['psychological_test_id']), $row);

        $this->assertDatabaseHas('psychological_question_translations', [
            'name' => $row['name'],
            'psychological_question_id' => PsychologicalQuestion::latest()->first()->id,
        ]);
    }


    public function test_edit_psychological_questions()
    {
        dump('test_edit_psychological_questions');
        
        $this->authAdmin();

        $record = factory(PsychologicalQuestion::class)->create();
        $row = factory(PsychologicalQuestion::class)->make()->toArray();

        $data = [
            'name:en' => $row['name'],
            'name:ar' => $row['name'],
        ];

        $row = array_merge($row, $data);

        $response = $this
            ->put(route('admin.psychological_questions.put.edit', $record->id), $row);

        $record = PsychologicalQuestion::find($record->id);

        $this->assertEquals($row['name'], $record->fresh()->name);
    }

    public function test_delete_psychological_questions()
    {
        dump('test_delete_psychological_questions');

        $this->authAdmin();

        $record = PsychologicalQuestion::create(factory(PsychologicalQuestion::class)->make()->toArray());

        $response = $this
            ->withSession(['locale' => 'en'])
            ->delete(route('admin.psychological_questions.delete', $record->id));

        $this->assertSoftDeleted('psychological_questions', [
            'id' => $record->id,
        ]);

        $this->assertNotNull($record->fresh()->deleted_at);

        $response->assertStatus(302);
    }
}
