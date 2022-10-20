<?php

namespace Database\Seeders;

use App\Modules\Courses\Models\Course;
use App\Modules\Courses\Models\OfferCourse;
use App\Modules\Offers\Models\Offer;
use Illuminate\Database\Seeder;

class OfferCoursesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $offers = Offer::get();
        foreach ($offers as $offer){
            $courses = Course::inRandomOrder()->take(3)->get();
            foreach ($courses as $course){
                $offerCourse = new OfferCourse;
                $offerCourse->course_id = $course->id;
                $offerCourse->offer_id = $offer->id;
                $offerCourse->save();
            }
        }
    }
}
