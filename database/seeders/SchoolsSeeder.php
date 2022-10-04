<?php

namespace Database\Seeders;

use App\OurEdu\Schools\School;
use Illuminate\Database\Seeder;

class SchoolsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        School::factory()->count(4)->create();
    }
}
