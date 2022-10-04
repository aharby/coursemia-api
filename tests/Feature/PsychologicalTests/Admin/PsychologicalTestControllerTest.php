<?php

namespace Tests\Feature\PsychologicalTests\Admin;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\WithFaker;
use App\OurEdu\PsychologicalTests\Models\PsychologicalTest;

class PsychologicalTestsControllerTest extends TestCase
{
    use WithFaker;

    /**
     * A basic test example.
     *
     * @return void
     */

    public function test_list_psychological_tests()
    {
        dump('test_list_psychological_tests');
        $this->authAdmin();

        create(PsychologicalTest::class);

        $response = $this
            ->withSession(['locale' => 'en'])
            ->get(route('admin.psychological_tests.get.index'))
            ->assertStatus(200);
    }

    public function test_create_psychological_tests()
    {
        dump('test_create_psychological_tests');

        $this->disableExceptionHandling();

        $this->authAdmin();
        
        $row = factory(PsychologicalTest::class)->make()->toArray();

        $data = [
            'picture'   =>  UploadedFile::fake()->image('avatar.png'),
            'name:en' => $row['name'],
            'name:ar' => $row['name'],
            'instructions:en' => $row['instructions'],
            'instructions:ar' => $row['instructions'],
        ];

        $row = array_merge($row, $data);

        $response = $this
            ->post(route('admin.psychological_tests.post.create'), $row)
            ->assertStatus(302);

        $this->assertDatabaseHas('psychological_test_translations', [
            'name' => $row['name:en'],
            'psychological_test_id' =>  PsychologicalTest::latest()->first()->id
        ]);
    }


    public function test_edit_psychological_tests()
    {
        dump('test_edit_psychological_tests');
        
        $this->authAdmin();

        $record = factory(PsychologicalTest::class)->create();
        $row = factory(PsychologicalTest::class)->make()->toArray();

        $data = [
            'name:en' => $row['name'],
            'name:ar' => $row['name'],
            'instructions:en' => $row['instructions'],
            'instructions:ar' => $row['instructions'],
        ];

        $row = array_merge($row, $data);

        $response = $this
            ->put(route('admin.psychological_tests.put.edit', $record->id), $row);

        $record = PsychologicalTest::find($record->id);

        $this->assertEquals($row['name'], $record->fresh()->name);
        $this->assertEquals($row['instructions'], $record->fresh()->instructions);
    }

    public function test_delete_psychological_tests()
    {
        dump('test_delete_psychological_tests');

        $this->authAdmin();

        $record = PsychologicalTest::create(factory(PsychologicalTest::class)->make()->toArray());

        $response = $this
            ->withSession(['locale' => 'en'])
            ->delete(route('admin.psychological_tests.delete', $record->id));

        $this->assertSoftDeleted('psychological_tests', [
            'id' => $record->id,
        ]);

        $this->assertNotNull($record->fresh()->deleted_at);

        $response->assertStatus(302);
    }
}
