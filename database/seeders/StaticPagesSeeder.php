<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StaticPagesSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (app()->environment() != 'production') {
            dump('Deleting last Data');
            DB::table('static_pages')->delete();
            DB::table('static_blocks')->delete();
            DB::table('static_page_translations')->delete();
            DB::table('static_block_translations')->delete();
        }
        dump('Static Pages Seeder');
        $this->call(HomePageStaticData::class);
        $this->call(AboutUsPageStaticData::class);
        $this->call(NewsPageSeeder::class);
        $this->call(PrivacyPolicyPageSeeder::class);
        $this->call(TermsAndConditionsPageSeeder::class);
        $this->call(TestAndEvaluationPageSeeder::class);
        $this->call(TestimonialsPageSeeder::class);
        $this->call(BeInstructorStaticDataSeeder::class);
        $this->call(PrivacyPolicyPage1Seeder::class);
        $this->call(TermsConditionPage1Seeder::class);
    }
}
