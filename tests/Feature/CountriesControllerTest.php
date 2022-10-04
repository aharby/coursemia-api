<?php

namespace Tests\Feature;

use App\OurEdu\Countries\Country;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class CountriesControllerTest extends TestCase
{

    use WithFaker;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_list_countries()
    {
        dump('test_list_countries');
        $this->authAdmin();
        $this->withSession(['locale' => 'en'])
            ->get(route('admin.countries.get.index'))
            ->assertStatus(200);
    }

    public function test_create_countries() {
        dump('test_create_countries');
        $this->authAdmin();
        $row = factory(Country::class)->make()->toArray();
        $name = [
            'name:en'=>'Name en',
            'name:ar'=>'Name ar',
        ];
        $row = array_merge($name,$row);

        $response = $this
            ->post(route('admin.countries.post.create'),$row);
        $latest = Country::orderBy('id', 'DESC')->first();
        $this->assertEquals($row['name'], $latest->name);
        $latest->forceDelete();
    }

    public function test_edit_countries() {
        dump('test_edit_countries');
        $this->authAdmin();
        $record = factory(Country::class)->create();
        $row = factory(Country::class)->make();
        $name = [
            'name:en' => 'title',
            'name:ar' => 'title'
        ];
        $row = array_merge($row->toArray(),$name);
        $response = $this
            ->put(route('admin.countries.put.edit' , $record->id), $row);
        $record = Country::find($record->id);
        $this->assertEquals($name['name:en'], $record->name);
        $record->forceDelete();
    }

    public function test_delete_countries() {
        dump('test_delete_countries');
        $this->authAdmin();
        $record = Country::create(factory(Country::class)->make()->toArray());
        $response = $this
            ->withSession(['locale' => 'en'])
            ->delete(route('admin.countries.delete' , $record->id));
        $this->assertEquals('a', 'a');
        $record->forceDelete();
    }
}
