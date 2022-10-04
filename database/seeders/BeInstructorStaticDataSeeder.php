<?php

namespace Database\Seeders;

use App\OurEdu\StaticBlocks\StaticBlock;
use App\OurEdu\StaticBlocks\StaticBlockTranslation;
use App\OurEdu\StaticPages\StaticPage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class BeInstructorStaticDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        dump('Be Instructor Page Seeder');
        $beInstructor = [
            'slug' => 'be-instructor',
            'url' => null,
            'bg_image' => null,
            'is_active' => 1,
            'title:en' => 'Be Instructor',
            'title:ar' => 'كن معلم',
            'body:en' => null,
            'body:ar' => null
        ];

        $beInstructor =  StaticPage::create($beInstructor);

        // top header
        $blocks[] = [
            'slug' => 'be-instructor-top-header',
            'url' => null,
            'is_active' => 1,
            'bg_image' => null,
            'icon' => null,
            'title' => 'top header',
            'body' => null,
        ];

        // header
        $blocks[] = [
            'slug' => 'be-instructor-header',
            'url' => null,
            'is_active' => 1,
            'bg_image' => null,
            'icon' => null,
            'title' => 'Header',
            'body' =>  null,
        ];

        //Nav-bar
        $blocks[] = [
            'slug' => 'be-instructor-nav-bar',
            'url' => null,
            'is_active' => 1,
            'bg_image' => null,
            'icon' => null,
            'title' => 'Navigation',
            'body' => null
        ];



        // body
        $blocks[] = [
            'slug' => 'be-instructor-body',
            'url' => null,
            'is_active' => 1,
            'bg_image' => null,
            'icon' => null,
            'title' => 'Be Instructor',
            'body' => null,
        ];



        // footer
        $blocks[] = [
            'slug' => 'be-instructor-footer',
            'url' => null,
            'is_active' => 1,
            'bg_image' => null,
            'icon' => null,
            'title' => 'Footer',
            'body' =>  null,
        ];



        foreach ($blocks as $key => $block) {
            $block = Arr::add($block, 'page_id', $beInstructor->id);
            $staticBlock = StaticBlock::create(Arr::except($block, 'child'));
            $otherLocale = app()->getLocale() == 'ar' ? 'en' : 'ar';
            $staticBlockTranslation = StaticBlockTranslation::create([
                'title' => $staticBlock->title,
                'body' => $staticBlock->body,
                'locale' => $otherLocale,
                'static_block_id' => $staticBlock->id
            ]);

            if (isset($block['child'])) {
                foreach ($block['child'] as $child) {
                    $child = Arr::add($child, 'page_id', $beInstructor->id);
                    $childBlock = StaticBlock::create($child);
                    $childBlockTranslation = StaticBlockTranslation::create([
                        'title' => $childBlock->title,
                        'body' => $childBlock->body,
                        'locale' => $otherLocale,
                        'static_block_id' => $childBlock->id
                    ]);
                    $staticBlock->childBlocks()->save($childBlock);
                }
            }
        }
    }
}
