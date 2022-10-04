<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\OurEdu\Countries\Country;
use App\OurEdu\Countries\CountryTranslation;

class CountriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Country::factory()->create();  
    }
}
