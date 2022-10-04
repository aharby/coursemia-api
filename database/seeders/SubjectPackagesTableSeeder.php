<?php

namespace Database\Seeders;

use App\OurEdu\SubjectPackages\Package;
use Illuminate\Database\Seeder;

class SubjectPackagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Package::factory()->create();
    }
}
