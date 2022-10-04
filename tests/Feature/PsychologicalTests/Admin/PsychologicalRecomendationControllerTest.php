<?php

namespace Tests\Feature\PsychologicalTests\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use App\OurEdu\PsychologicalTests\Models\SubModels\PsychologicalRecomendation;

class PsychologicalRecomendationsControllerTest extends TestCase
{
    use WithFaker;

    /**
     * A basic test example.
     *
     * @return void
     */

    public function test_list_psychological_recomendations()
    {
        dump('test_list_psychological_recomendations');
        $this->disableExceptionHandling();

        $this->authAdmin();

        $row = create(PsychologicalRecomendation::class);

        $response = $this
            ->withSession(['locale' => 'en'])
            ->get(route('admin.psychological_recomendations.get.index', $row->psychological_test_id))
            ->assertStatus(200);
    }

    public function test_create_psychological_recomendations()
    {
        dump('test_create_psychological_recomendations');

        $this->disableExceptionHandling();

        $this->authAdmin();
        
        $row = factory(PsychologicalRecomendation::class)->make()->toArray();

        $data = [
            'result:en' => $row['result'],
            'result:ar' => $row['result'],
            'recomendation:en' => $row['recomendation'],
            'recomendation:ar' => $row['recomendation'],
        ];

        $row = array_merge($row, $data);

        $response = $this
            ->post(route('admin.psychological_recomendations.post.create', $row['psychological_test_id']), $row)
            ->assertStatus(302);

        $this->assertDatabaseHas('psychological_recomendation_translations', [
            'result' => $row['result'],
            'psychological_recomendation_id' => PsychologicalRecomendation::latest()->first()->id,
        ]);
    }


    public function test_edit_psychological_recomendations()
    {
        dump('test_edit_psychological_recomendations');
        
        $this->authAdmin();

        $record = factory(PsychologicalRecomendation::class)->create();
        $row = factory(PsychologicalRecomendation::class)->make()->toArray();

        $data = [
            'result:en' => $row['result'],
            'result:ar' => $row['result'],
            'recomendation:en' => $row['recomendation'],
            'recomendation:ar' => $row['recomendation'],
        ];

        $row = array_merge($row, $data);

        $response = $this
            ->put(route('admin.psychological_recomendations.put.edit', $record->id), $row);

        $record = PsychologicalRecomendation::find($record->id);

        $this->assertEquals($row['result'], $record->fresh()->result);
    }

    public function test_delete_psychological_recomendations()
    {
        dump('test_delete_psychological_recomendations');

        $this->authAdmin();

        $record = PsychologicalRecomendation::create(factory(PsychologicalRecomendation::class)->make()->toArray());

        $response = $this
            ->withSession(['locale' => 'en'])
            ->delete(route('admin.psychological_recomendations.delete', $record->id));

        $this->assertSoftDeleted('psychological_recomendations', [
            'id' => $record->id,
        ]);

        $this->assertNotNull($record->fresh()->deleted_at);

        $response->assertStatus(302);
    }
}
