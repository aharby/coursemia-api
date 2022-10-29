<?php

namespace Database\Seeders;

use App\Modules\Specialities\Models\Speciality;
use Illuminate\Database\Seeder;

class SpecialitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $index = 0;
        $images = [
            '1665320145qvrbmgpuhc.png',
            '1665422398sqcyikytdr.png',
            '1665422466xtdlvieyej.png',
            '1665422552temowpnrxt.png',
            '1665423169dtajncunlo.jpg',
        ];
        while ($index < 100) {
            $speciality = [
                'image' => 'specialities/' . $images[array_rand($images)],
                'is_active' => 1,
                'title:en' => "speciality ${index} en",
                'title:ar' => "speciality ${index} ar"
            ];
            Speciality::create($speciality);
            $index++;
        }
    }
}
