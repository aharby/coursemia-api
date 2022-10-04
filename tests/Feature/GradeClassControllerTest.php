<?php

namespace Tests\Feature;

use App\OurEdu\Countries\Country;
use App\OurEdu\EducationalSystems\EducationalSystem;
use App\OurEdu\GradeClasses\GradeClass;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GradeClassControllerTest extends TestCase
{
    use WithFaker;
    /**
     * A basic test example.
     *
     * @return void
     */

    public function test_list_grade_Classes()
    {
        dump('test_list_grade_Classes');
        $this->authAdmin();
        $response = $this
            ->withSession(['locale' => 'en'])
            ->get(route('admin.gradeClasses.get.index'))
            ->assertStatus(200);
    }

    public function test_create_grade_Classes()
    {
        dump('test_create_grade_Classes');
        $this->authAdmin();
        
        $country = Country::first();
        $educationalSystem = EducationalSystem::first();
        $data = [
            'title:en' => 'title en',
            'title:ar' => 'title ar',
            'is_active' => 1,
            'country_id' => $country->id,
            'educational_system_id' => $educationalSystem->id,
        ];
        $response = $this
            ->post(route('admin.gradeClasses.post.create'), $data);
        $this->assertDatabaseHas('grade_classes', [
            'is_active' => $data['is_active'],
            'country_id' => $data['country_id'],
            'educational_system_id' => $data['educational_system_id'],
        ]);

        $this->assertDatabaseHas('grade_class_translations', [
            'title' => $data['title:en'],
            'locale' => 'en',
        ]);
    }

    public function test_create_grade_class_fail()
    {
        dump('test_create_grade_class_fail');
        $this->authAdmin();

        // Required failed
        $response = $this
            ->post(route('admin.gradeClasses.post.create'));
        $response->assertSessionHasErrors([
            'title:ar',
            'title:en',
            'country_id',
            'educational_system_id',
        ]);
    }

    public function test_edit_grade_Classes()
    {
        dump('test_edit_grade_Classes');
        $this->authAdmin();
        $record = factory(GradeClass::class)->create();
        $row = factory(GradeClass::class)->make();
        $name = [
            'title:en' => 'title en en',
            'title:ar' => 'title ar ar'
        ];
        $row = array_merge($row->toArray(), $name);
        $response = $this
            ->put(route('admin.gradeClasses.put.edit', $record->id), $row);
        $record = GradeClass::find($record->id);
        $this->assertEquals($row['title:en'], $record->translate('en')->title);
    }

    public function test_delete_grade_Classes()
    {
        dump('test_delete_grade_Classes');
        $this->authAdmin();
        $record = GradeClass::create(factory(GradeClass::class)->make()->toArray());
        $response = $this
            ->withSession(['locale' => 'en'])
            ->delete(route('admin.gradeClasses.delete', $record->id));

        $this->assertSoftDeleted('grade_classes', [
            'id' => $record->id,
            'country_id' => $record->country_id,
            'educational_system_id' => $record->educational_system_id,

            ]);

        $response->assertStatus(302);
    }
}
