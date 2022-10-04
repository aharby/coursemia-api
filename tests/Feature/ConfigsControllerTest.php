<?php

namespace Tests\Feature;

use App\OurEdu\Config\Config;
use Tests\TestCase;

class ConfigsControllerTest extends TestCase
{


    public function test_edit_configs() {
        dump('test_edit_configs');
        $this->authAdmin();
        $record = Config::create(factory(Config::class)->make()->toArray());
        $row = factory(Config::class)->make();
        $response = $this->post('configs/edit/' . $record->id, $row->toArray());
        $record = Config::find($record->id);
        $this->assertEquals($record->title, $row->title);
        $record->forceDelete();
    }



}
