<?php

namespace Database\Factories;

use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropData;
use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropOption;
use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

class DragDropQuestionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DragDropQuestion::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $dataId = DragDropData::first()->id ?? DragDropData::factory()->create()->id;

        return [
            // for making questions with math equations
            //        'question'	=>	randomEquation(). '*__*',
            // for making questions with normal text
            'question'    =>    $this->faker->sentence() . '*__*',
            'res_drag_drop_data_id'    =>    $dataId,
            'correct_option_id' =>  DragDropOption::where('res_drag_drop_data_id', $dataId)->first()->id ?? DragDropOption::factory()->create(['res_drag_drop_data_id' => $dataId])->id,
        ];
    }
}
