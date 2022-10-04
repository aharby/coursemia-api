<?php

namespace Database\Factories;

use App\OurEdu\GarbageMedia\GarbageMedia;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;

class GarbageMediaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = GarbageMedia::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $file = UploadedFile::fake()->image('avatar.jpg', 400, 400);

        $name = time() . str_random(4) . '.' . $file->getClientOriginalExtension();

        $file->storeAs('public/garbage_media', $name);

        return [
            'source_filename' => $file->getClientOriginalName(),
            'filename' => $name,
            'mime_type' => $file->getClientMimeType(),
            'url' => null,
            'extension' => $file->getClientOriginalExtension(),
        ];
    }

    public function pdf()
    {
        $file = UploadedFile::fake()->create('document.pdf', 100);

        $name = time() . str_random(4) . '.' . $file->getClientOriginalExtension();

        $file->storeAs('public/garbage_media', $name);

        return $this->state([
            'source_filename' => $file->getClientOriginalName(),
            'filename' => $name,
            'mime_type' => $file->getClientMimeType(),
            'url' => null,
            'extension' => $file->getClientOriginalExtension(),
        ]);
    }

    public function video()
    {
        $file = UploadedFile::fake()->create('video.mp4', 100);

        $name = time() . str_random(4) . '.' . $file->getClientOriginalExtension();

        $file->storeAs('public/garbage_media', $name);

        return $this->state([
            'source_filename' => $file->getClientOriginalName(),
            'filename' => $name,
            'mime_type' => $file->getClientMimeType(),
            'url' => null,
            'extension' => $file->getClientOriginalExtension(),
        ]);
    }

    public function audio()
    {
        $file = UploadedFile::fake()->create('audio.mp3', 100);

        $name = time() . str_random(4) . '.' . $file->getClientOriginalExtension();

        $file->storeAs('public/garbage_media', $name);

        return $this->state([
            'source_filename' => $file->getClientOriginalName(),
            'filename' => $name,
            'mime_type' => $file->getClientMimeType(),
            'url' => null,
            'extension' => $file->getClientOriginalExtension(),
        ]);
    }

    public function flash()
    {
        $file = UploadedFile::fake()->create('audio.swf', 100);

        $name = time() . str_random(4) . '.' . $file->getClientOriginalExtension();

        $file->storeAs('public/garbage_media', $name);

        return $this->state([
            'source_filename' => $file->getClientOriginalName(),
            'filename' => $name,
            'mime_type' => $file->getClientMimeType(),
            'url' => null,
            'extension' => $file->getClientOriginalExtension(),
        ]);
    }
}
