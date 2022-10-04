<?php

namespace Database\Factories;

use App\OurEdu\Exams\Enums\ExamTypes;
use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\LearningResources\Enums\DifficultlyLevelEnums;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class ExamFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Exam::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'student_id' => Student::factory()->create()->id,
            'subject_id' => Subject::first()->id ?? Subject::factory()->create()->id,
            'questions_number' => random_int(10, 20),
            'difficulty_level' => Arr::random(DifficultlyLevelEnums::availableDifficultlyLevel()),
            'type'    =>    array_random([ExamTypes::EXAM, ExamTypes::COMPETITION, ExamTypes::PRACTICE]),
            'is_finished' => false,
            'is_started' => true,
            'start_time' => date('Y-m-d H:i:s'),
            'result'    =>    random_int(1, 100)
        ];
    }
}
