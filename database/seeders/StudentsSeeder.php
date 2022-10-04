<?php

namespace Database\Seeders;

use App\OurEdu\Users\Models\Student;
use Illuminate\Database\Seeder;

class StudentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Student::factory()->count(4)->create();
    }
}
