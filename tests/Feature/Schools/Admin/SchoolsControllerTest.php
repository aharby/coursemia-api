<?php

namespace Tests\Feature;

use App\OurEdu\Schools\School;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SchoolsControllerTest extends TestCase
{
    use WithFaker;

    /**
     * A basic test example.
     *
     * @return void
     */

    public function test_list_schools()
    {
        dump('test_list_schools');
        $this->authAdmin();

        $response = $this
            ->withSession(['locale' => 'en'])
            ->get(route('admin.schools.get.index'))
            ->assertStatus(200);
    }

    public function test_create_schools()
    {
        dump('test_create_schools');
        $this->authAdmin();
        
        $row = factory(School::class)->make()->toArray();
        $row['name:en'] = 'title en';
        $row['name:ar'] = 'title ar';

        $response = $this
            ->post(route('admin.schools.post.create'), $row);
        $this->assertDatabaseHas('schools', [
            'is_active' => $row['is_active'],
            'country_id' => $row['country_id'],
            'educational_system_id' => $row['educational_system_id'],
        ]);

        $this->assertDatabaseHas('school_translations', [
            'name' => $row['name:en'],
            'locale' => 'en',
        ]);
    }


    public function test_edit_schools()
    {
        dump('test_edit_schools');
        
        $this->authAdmin();
        $record = factory(School::class)->create();
        $row = factory(School::class)->make()->toArray();
        $row['name:en'] = 'name en';
        $row['name:ar'] = 'name ar';
        $response = $this
            ->put(route('admin.schools.put.edit', $record->id), $row);
        $record = School::find($record->id);
        $this->assertEquals($row['name:en'], $record->translate('en')->name);
    }

    public function test_delete_schools()
    {
        dump('test_delete_schools');
        $this->authAdmin();
        $record = School::create(factory(School::class)->make()->toArray());
        $response = $this
            ->withSession(['locale' => 'en'])
            ->delete(route('admin.schools.delete', $record->id));

        $this->assertSoftDeleted('schools', [
            'id' => $record->id,
            'country_id' => $record->country_id,
            'educational_system_id' => $record->educational_system_id,

        ]);

        $response->assertStatus(302);
    }
}
