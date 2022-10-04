<?php

namespace Database\Seeders;

use App\OurEdu\Feedbacks\Feedback;
use Illuminate\Database\Seeder;

class FeedbacksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Feedback::factory()->count(5)->create();
    }
}
