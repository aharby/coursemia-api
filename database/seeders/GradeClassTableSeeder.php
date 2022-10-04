<?php

namespace Database\Seeders;

use App\OurEdu\GradeClasses\GradeClass;
use Illuminate\Database\Seeder;

class GradeClassTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        GradeClass::factory()->create();
    }
}
