<?php

namespace Database\Factories;

use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropData;
use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropOption;
use Illuminate\Database\Eloquent\Factories\Factory;

class DragDropOptionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DragDropOption::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'option' => $this->faker->sentence(),
            'res_drag_drop_data_id'    =>    DragDropData::first()->id ?? DragDropData::factory()->create()->id,
        ];
    }
}
