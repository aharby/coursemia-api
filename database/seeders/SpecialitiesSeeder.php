<?php

namespace Database\Seeders;

use App\Modules\Specialities\Models\Speciality;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

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
        $images = Storage::allFiles('uploads/large/specialities/');
        while ($index < 100) {
            $speciality = [
                'image' => str_replace('uploads/large/', '', $images[array_rand($images)]),
                'is_active' => 1,
                'title:en' => "speciality ${index} en",
                'title:ar' => "عنوان ${index} ar"
            ];
            Speciality::create($speciality);
            $index++;
        }
    }
}
