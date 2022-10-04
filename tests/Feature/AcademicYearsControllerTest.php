<?php

namespace Tests\Feature;

use App\OurEdu\AcademicYears\AcademicYear;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class AcademicYearsControllerTest extends TestCase
{
    // Refresh the database before running the unit test to make sure the failures isn't because of the data
    //use RefreshDatabase;
    use WithFaker;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_list_academic_years()
    {
        dump('test_list_academic_years');
        $this->authAdmin();
        $response = $this
            ->withSession(['locale' => 'en'])
            ->get(route('admin.academicYears.get.index'))
            ->assertStatus(200);
    }

    public function test_create_academic_years()
    {
        dump('test_create_academic_years');
        $this->authAdmin();
        $row = factory(AcademicYear::class)->make()->toArray();
        $name = [
            'name:en'=>'Name en',
            'name:ar'=>'Name ar',
        ];
        $row = array_merge($name,$row);
        $response = $this
            ->post(route('admin.academicYears.post.create'),$row);

        $latest = AcademicYear::orderBy('id', 'DESC')->first();
        $this->assertEquals($row['name'], $latest->name);
        $latest->forceDelete();
    }

    public function test_edit_academic_years()
    {
        dump('test_edit_academic_years');
        $this->authAdmin();
        $record = factory(AcademicYear::class)->create();
        $row = factory(AcademicYear::class)->make();
        $name = [
            'name:en' => 'title',
            'name:ar' => 'title'
        ];
        $row = array_merge($row->toArray(),$name);
        $response = $this
                    ->put(route('admin.academicYears.put.edit' , $record->id), $row);
        $record = AcademicYear::find($record->id);
        $this->assertEquals($row['name:en'], $record->name);
        $record->forceDelete();
    }

    public function test_delete_academic_years()
    {
        dump('test_delete_academic_years');
        $this->authAdmin();
        $record = AcademicYear::create(factory(AcademicYear::class)->make()->toArray());
        $response = $this
            ->withSession(['locale' => 'en'])
            ->delete(route('admin.academicYears.delete' , $record->id));
        $this->assertEquals('a', 'a');
        $record->forceDelete();
    }
}
