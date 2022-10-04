<?php

namespace Database\Factories;

use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubjectFormatSubjectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SubjectFormatSubject::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->name,
            'is_active' => 1,
            'subject_id' => Subject::first()->id ?? Subject::factory()->create()->id,
            'parent_subject_format_id' => null,
            'is_editable' => 1,
        ];
    }
}
