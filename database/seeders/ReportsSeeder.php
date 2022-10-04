<?php

namespace Database\Seeders;

use App\OurEdu\Reports\Report;
use Illuminate\Database\Seeder;

class ReportsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Report::factory()->create();
    }
}
