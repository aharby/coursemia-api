<?php

namespace Tests\Feature\GrabageMedia;

use App\OurEdu\GarbageMedia\GarbageMedia;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;

class GarbageMediaControllerTest extends TestCase
{
    public function test_post_images()
    {
        dump('test_garbage_media');
        
        $user = $this->authSME();
        $file = UploadedFile::fake()->image('avatar.jpg');
        $response = $this->json('post', '/api/v1/en/image', [
            'images' => [$file]
        ], $this->loginUsingHeader($user))->assertStatus(200)
            ->assertJsonStructure([
                "data" => [
                    [
                        "type",
                        'id',
                        'attributes'
                    ]
                ]
            ]);
        $contents = json_decode($response->getContent());

        foreach ($contents as $content) {
            foreach ($content as $c) {
                $garbageMedia = GarbageMedia::whereId($c->id)->first();
                \File::delete(storage_path('garbage_media/'.$garbageMedia->filename));
            };
        }
    }
}
