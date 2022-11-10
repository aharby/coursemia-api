<?php

namespace Database\Seeders;

use App\Modules\Countries\Models\Country;
use App\Modules\Courses\Models\HostCourseRequest;
use App\Modules\Specialities\Models\Speciality;
use Faker\Factory;
use Illuminate\Database\Seeder;

class HostCourseRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $factory = Factory::create();
        $index = 0;
        $requests = [];
        $countries = Country::all();
        $specialities = Speciality::all();
        while ($index < 100) {
            $country = $countries->random();
            $hostRequest = [
                'name' => $factory->name,
                'email' => $factory->email,
                'about_course' => $factory->realText(150),
                'mobile' => $country->country_code . $factory->phoneNumber,
                'country_id' => $country->id,
                'speciality_id' => $specialities->random()->id,
            ];
            array_push($requests, $hostRequest);
            $index++;
        }
        HostCourseRequest::insert($requests);
    }
}
