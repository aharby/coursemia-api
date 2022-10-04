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
        $this->call(DummySeeder::class);
        $this->call(BasicSeeder::class);
        //        $this->call(CountriesTableSeeder::class);
        //        $this->call(EducationalSystemsTableSeeder::class);
        //        $this->call(AcademicYearsTableSeeder::class);
        //        $this->call(SchoolsSeeder::class);
        $this->call(ResourcesTableSeeder::class);
        $this->call(RemoveParentsEmptyStudent::class);

        //        $this->call(CreateDummySubjectSeeder::class);
        $this->call(StaticPagesSeeder::class);
    }
}
