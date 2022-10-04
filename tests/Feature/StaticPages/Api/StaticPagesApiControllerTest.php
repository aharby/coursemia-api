<?php

namespace Tests\Feature;

use App\OurEdu\StaticBlocks\StaticBlock;
use App\OurEdu\StaticPages\StaticPage;
use Tests\TestCase;

class StaticPagesApiControllerTest extends TestCase
{
    public function test_get_static_page_or_static_block() {
        dump('test_get_static_page_or_static_block');
        $this->seed('StaticPagesSeeder');
        $staticPageSlug = 'homepage';
        $staticBlockSlug = 'homepage-top-header';

        // getting a specific block in a specific page
        $response = $this->getJson('api/v1/en/static-pages/'.$staticPageSlug.'/'.$staticBlockSlug);
        $response->assertOk();

        // getting a specific block in a specific page with its children blocks
        $response = $this->get('api/v1/en/static-pages/'.$staticPageSlug.'/'.$staticBlockSlug.'?include=blocks');
        $response->assertOk();
    }
}
