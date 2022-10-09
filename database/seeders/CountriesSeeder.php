<?php

namespace Database\Seeders;

use App\Modules\Countries\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
        while ($index < 10 ){
            $country =[
                'flag' => 'eg.jpg',
                'is_active'=>1,
                'country_code'=> 20,
                'title:en'=>"country ${index} en",
                'title:ar'=>"country ${index} ar"
            ];
            Country::create($country);
            $index++;
        }
        DB::table('countries')->update(['country_code' => DB::raw("CONCAT('+',country_code)")]);
    }
}
