<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\OurEdu\Countries\CountryTranslation;

class CreateCountriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CountryTranslation::where('locale','en')->update(['currency'=>'SAR']);
        CountryTranslation::where('locale','ar')->update(['currency'=>'ريال سعودى']);
    }
}
