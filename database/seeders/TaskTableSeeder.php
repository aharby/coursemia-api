<?php

namespace Database\Seeders;

use App\OurEdu\Subjects\Models\SubModels\Task;
use Illuminate\Database\Seeder;

class TaskTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Task::factory()->create();
    }
}
