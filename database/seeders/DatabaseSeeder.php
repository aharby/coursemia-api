<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
//        $this->call(DummySeeder::class);
//        $this->call(BasicSeeder::class);
        //        $this->call(CountriesTableSeeder::class);
        //        $this->call(EducationalSystemsTableSeeder::class);
        //        $this->call(AcademicYearsTableSeeder::class);
        //        $this->call(SchoolsSeeder::class);
//        $this->call(ResourcesTableSeeder::class);
//        $this->call(RemoveParentsEmptyStudent::class);

        //        $this->call(CreateDummySubjectSeeder::class);
//        $this->call(SpecialitiesSeeder::class);
//        $this->call(CountriesSeeder::class);
//        $this->call(CategorySeeder::class);
//        $this->call(CoursesSeeder::class);
//        $this->call(OffersSeeder::class);
//        $this->call(EventsSeeder::class);
//        $this->call(UsersSeeder::class);
//        $this->call(QuestionsAndAnswersSeeder::class);
//        $this->call(FlashcardsSeeder::class);
//        $this->call(LecturesSeeder::class);
//        $this->call(NotesSeeder::class);
//        $this->call(CourseUserSeeder::class);
//        $this->call(CourseReviewsSeeder::class);
//        $this->call(CourseImageSeeder::class);
//        $this->call(WantToLearnSeeder::class);
        $this->call(SettingsSeeder::class);
        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(SuperAdminSeeder::class);
    }
}
