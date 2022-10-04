<?php

namespace Database\Seeders;

use App\OurEdu\EducationalSystems\EducationalSystem;
use Illuminate\Database\Seeder;

class EducationalSystemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        EducationalSystem::factory()->create();
    }
}
