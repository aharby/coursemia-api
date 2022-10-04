<?php

namespace Database\Seeders;

use App\OurEdu\AcademicYears\AcademicYear;
use Illuminate\Database\Seeder;

class AcademicYearsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AcademicYear::factory()->create();
    }
}
