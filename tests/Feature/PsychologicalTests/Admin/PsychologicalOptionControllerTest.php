<?php

namespace Tests\Feature\PsychologicalTests\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use App\OurEdu\PsychologicalTests\Models\SubModels\PsychologicalOption;

class PsychologicalOptionsControllerTest extends TestCase
{
    use WithFaker;

    /**
     * A basic test example.
     *
     * @return void
     */

    public function test_list_psychological_options()
    {
        dump('test_list_psychological_options');
        $this->disableExceptionHandling();

        $this->authAdmin();

        $row = create(PsychologicalOption::class);

        $response = $this
            ->withSession(['locale' => 'en'])
            ->get(route('admin.psychological_options.get.index', $row->psychological_test_id))
            ->assertStatus(200);
    }

    public function test_create_psychological_options()
    {
        dump('test_create_psychological_options');

        $this->disableExceptionHandling();
        
        $this->authAdmin();
        
        $row = factory(PsychologicalOption::class)->make()->toArray();

        $data = [
            'name:en' => $row['name'],
            'name:ar' => $row['name'],
        ];

        $row = array_merge($row, $data);

        $response = $this
            ->post(route('admin.psychological_options.post.create', $row['psychological_test_id']), $row);

        $this->assertDatabaseHas('psychological_option_translations', [
            'name' => $row['name'],
            'psychological_option_id' => PsychologicalOption::latest()->first()->id,
        ]);
    }


    public function test_edit_psychological_options()
    {
        dump('test_edit_psychological_options');
        
        $this->authAdmin();

        $record = factory(PsychologicalOption::class)->create();
        $row = factory(PsychologicalOption::class)->make()->toArray();

        $data = [
            'name:en' => $row['name'],
            'name:ar' => $row['name'],
        ];

        $row = array_merge($row, $data);

        $response = $this
            ->put(route('admin.psychological_options.put.edit', $record->id), $row);

        $record = PsychologicalOption::find($record->id);

        $this->assertEquals($row['name'], $record->fresh()->name);
    }

    public function test_delete_psychological_options()
    {
        dump('test_delete_psychological_options');

        $this->authAdmin();

        $record = PsychologicalOption::create(factory(PsychologicalOption::class)->make()->toArray());

        $response = $this
            ->withSession(['locale' => 'en'])
            ->delete(route('admin.psychological_options.delete', $record->id));

        $this->assertSoftDeleted('psychological_options', [
            'id' => $record->id,
        ]);

        $this->assertNotNull($record->fresh()->deleted_at);

        $response->assertStatus(302);
    }
}
