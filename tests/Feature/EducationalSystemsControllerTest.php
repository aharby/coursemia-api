<?php

namespace Tests\Feature;

use App\OurEdu\EducationalSystems\EducationalSystem;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EducationalSystemsControllerTest extends TestCase
{
    use WithFaker;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_list_educational_systems()
    {
        dump('test_list_educational_systems');
        
        $this->authAdmin();
        $response = $this
            ->withSession(['locale' => 'en'])
            ->get(route('admin.educationalSystems.get.index'))
            ->assertStatus(200);
    }

    public function test_create_educational_systems()
    {
        dump('test_create_educational_systems');
        
        $this->authAdmin();
        $row = factory(EducationalSystem::class)->make()->toArray();
        $name = [
            'name:en'=>'Name en',
            'name:ar'=>'Name ar',
        ];
        $row = array_merge($name, $row);
        $response = $this
                ->post(route('admin.educationalSystems.post.create'), $row);
        $latest = EducationalSystem::orderBy('id', 'DESC')->first();
        $this->assertEquals($row['name'], $latest->name);
        $latest->forceDelete();
    }

    public function test_edit_educational_systems()
    {
        dump('test_edit_educational_systems');
        
        $this->authAdmin();
        $record = factory(EducationalSystem::class)->create();
        $row = factory(EducationalSystem::class)->make();
        $name = [
            'name:en' => 'title',
            'name:ar' => 'title'
        ];
        $row = array_merge($row->toArray(), $name);
        $response = $this
            ->put(route('admin.educationalSystems.put.edit', $record->id), $row);
        $record = EducationalSystem::find($record->id);
        $this->assertEquals($row['name:en'], $record->name);
        $record->forceDelete();
    }

    public function test_delete_educational_systems()
    {
        dump('test_delete_educational_systems');
        
        $this->authAdmin();
        $record = EducationalSystem::create(factory(EducationalSystem::class)->make()->toArray());
        $response = $this
            ->withSession(['locale' => 'en'])
            ->delete(route('admin.educationalSystems.delete', $record->id));
        $this->assertEquals('a', 'a');
        $record->forceDelete();
    }
}
