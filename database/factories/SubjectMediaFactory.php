<?php

namespace Database\Factories;

use App\OurEdu\GarbageMedia\MediaEnums;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Subjects\Models\SubModels\SubjectMedia;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;

class SubjectMediaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SubjectMedia::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $extension = array_random(MediaEnums::SUPPORTED_TYPES);

        $file = UploadedFile::fake()->create("file.{$extension}");

        $name = time() . str_random(4) . '.' . $file->getClientOriginalExtension();

        $file->storeAs('public/garbage_media', $name);

        return [
            'source_filename' => $file->getClientOriginalName(),
            'filename' => $name,
            'mime_type' => $file->getClientMimeType(),
            'url' => null,
            'extension' => $file->getClientOriginalExtension(),
            'subject_id'    =>    Subject::first()->id ?? Subject::factory()->create()->id
        ];
    }
}
