<?php

namespace Tests\Feature;

use App\OurEdu\Options\Option;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class OptionsControllerTest extends TestCase
{
    use WithFaker;

    /**
     * A basic test example.
     *
     * @return void
     */

    public function test_list_options()
    {
        dump('test_list_options');
        $this->authAdmin();

        $response = $this
            ->withSession(['locale' => 'en'])
            ->get(route('admin.options.get.index'))
            ->assertStatus(200);
    }

    public function test_create_options()
    {
        dump('test_create_options');
        $this->authAdmin();
        
        $row = factory(Option::class)->make()->toArray();
        $row['title:en'] = 'title en';
        $row['title:ar'] = 'title ar';

        $response = $this
            ->post(route('admin.options.post.create'), $row);
        $this->assertDatabaseHas('options', [
            'is_active' => $row['is_active'],
        ]);

        $this->assertDatabaseHas('option_translations', [
            'title' => $row['title:en'],
            'locale' => 'en',
        ]);
    }


    public function test_edit_options()
    {
        dump('test_edit_options');
        
        $this->authAdmin();
        $record = factory(Option::class)->create();
        $row = factory(Option::class)->make()->toArray();
        $row['title:en'] = 'name en';
        $row['title:ar'] = 'name ar';
        $response = $this
            ->put(route('admin.options.put.edit', $record->id), $row);
        $record = Option::find($record->id);
        $this->assertEquals($row['title:en'], $record->translate('en')->title);
    }

    public function test_delete_options()
    {
        dump('test_delete_options');
        $this->authAdmin();
        $record = Option::create(factory(Option::class)->make()->toArray());
        $response = $this
            ->withSession(['locale' => 'en'])
            ->delete(route('admin.options.delete', $record->id));

        $this->assertSoftDeleted('options', [
            'id' => $record->id,
            'is_active'=> $record->is_active
        ]);

        $response->assertStatus(302);
    }
}
