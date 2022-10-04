<?php

namespace Database\Factories;

use App\OurEdu\Reports\Report;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReportFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Report::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $subject = Subject::first()->id ?? Subject::factory()->create();
        $student = Student::factory()->create();
        $student->subscribe()->create([
            'subject_id' => $subject->id
        ]);

        return [
            'report'    =>    $this->faker->text,
            'reportable_id'    =>    $subject->id,
            'reportable_type'    =>    get_class($subject),
            'student_id'    =>    $student->id,
        ];
    }
}
