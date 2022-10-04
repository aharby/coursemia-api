<?php

namespace Database\Factories;

use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use App\OurEdu\Subjects\Models\SubModels\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $subjectFormat = SubjectFormatSubject::factory()->create();
        $resource = ResourceSubjectFormatSubject::factory()->create(['subject_format_subject_id' => $subjectFormat->id]);

        return [
            'title' => $this->faker->name,
            'is_active' => 1,
            'is_expired' => $this->faker->boolean(),
            'is_done' => $this->faker->boolean(),
            'is_assigned' => $this->faker->boolean(),
            'subject_format_subject_id'    => $subjectFormat->id,
            'resource_subject_format_subject_id' => $resource->id,
            'subject_id'    => $subjectFormat->subject_id,
        ];
    }
}
