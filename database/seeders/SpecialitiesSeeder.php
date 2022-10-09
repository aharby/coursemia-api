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
        for ($i = 1; $i <= 10; $i++){
            $speciality = new Speciality;
            $speciality->title_ar = "Arabic Speciality #$i";
            $speciality->title_en = "English Speciality #$i";
            $speciality->image = "/uploads/events/event-1664893285.png";
            $speciality->save();
        }
    }
}
