<?php

namespace Database\Seeders;

use App\Modules\Countries\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CountriesSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $index = 0;
        $flags = Storage::allFiles('uploads/large/countries/');
        while ($index < 100) {
            $country = [
                'flag' => str_replace('uploads/large/', '', $flags[array_rand($flags)]),
                'is_active' => 1,
                'country_code' => rand(1, 1000),
                'title:en' => "country ${index} en",
                'title:ar' => "عنوان ${index} ar"
            ];
            Country::create($country);
            $index++;
        }
        DB::table('countries')->update(['country_code' => DB::raw("CONCAT('+',country_code)")]);
    }
}
