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
        $flags = [
            '1665268849bfeeclcxql.png',
            '1665269224bdcqwnmfqx.png',
            '1665270122xezggasmel.png',
            '1665318858titubyjtgf.png',
            '1665319970efweevvkth.png',
            '1665319986pmhfmmgqyz.png',
            '1665320145qvrbmgpuhc.png',
            '1665417650htifgjxjwv.png',
            '1665422803vsrlzcbgcw.png',
            '1666815291vxrtejprju.jpg',
        ];
        while ($index < 100) {
            $country = [
                'flag' => 'countries/' . $flags[array_rand($flags)],
                'is_active' => 1,
                'country_code' => rand(1, 1000),
                'title:en' => "country ${index} en",
                'title:ar' => "country ${index} ar"
            ];
            Country::create($country);
            $index++;
        }
        DB::table('countries')->update(['country_code' => DB::raw("CONCAT('+',country_code)")]);
    }
}
