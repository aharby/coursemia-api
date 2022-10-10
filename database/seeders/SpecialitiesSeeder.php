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
        while ($index < 10) {
            $speciality =[
                'image' => "1665320145qvrbmgpuhc.png",
                'is_active'=>1,
                'title:en'=>"speciality ${index} en",
                'title:ar'=>"speciality ${index} ar"
            ];
            Speciality::create($speciality);
            $index++;
        }
    }
}
