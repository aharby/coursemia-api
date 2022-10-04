<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\OurEdu\StaticPages\StaticPage;
class TermsConditionPage1Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $page = StaticPage::where('id', 5)->with('translations')->first();
        dump('Terms And Conditions Page Seeder');
        $page = [
            'slug' => 'terms-and-conditions1',
            'url' => null,
            'bg_image' => null,
            'is_active' => 1,
            'title:en' => $page->translateOrDefault('en')->title,
            'title:ar' => $page->translateOrDefault('ar')->title,
            'body:en' => $page->translateOrDefault('en')->body,
            'body:ar' =>$page->translateOrDefault('ar')->title
        ];

        $page =  StaticPage::create($page);
    }
}
