<?php

namespace Database\Seeders;

use App\OurEdu\StaticBlocks\StaticBlock;
use App\OurEdu\StaticBlocks\StaticBlockTranslation;
use App\OurEdu\StaticPages\StaticPage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class TestimonialsPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        dump('Testimonials Seeder');
        $page = [
            'slug' => 'testimonials',
            'url' => null,
            'bg_image' => null,
            'is_active' => 1,
            'title:en' => 'Testimonials',
            'title:ar' => 'الشهادات',
            'body:en' => null,
            'body:ar' => null
        ];

        $page =  StaticPage::create($page);

        // top header
        $blocks[] = [
            'slug' => 'testimonials-top-header',
            'url' => null,
            'is_active' => 1,
            'bg_image' => null,
            'icon' => null,
            'title' => 'top header',
            'body' => null,
            'child' => [
                [
                    // Top header Logo
                    'slug' => 'testimonials-top-header-logo',
                    'url' => null,
                    'is_active' => 1,
                    'bg_image' => null,
                    'icon' => null,
                    'title' => 'Logo',
                    'body' => null
                ],
                [
                    // Top Header Menu
                    'slug' => 'testimonials-top-header-menu',
                    'url' => null,
                    'is_active' => 1,
                    'bg_image' => null,
                    'icon' => null,
                    'title' => 'Top Header Menu',
                    'body' => json_encode([
                        [
                            'title' => 'Testimonials',
                            'description' => null,
                            'link' => null,
                            'icon' => null
                        ]
                    ]),
                ]
            ]
        ];

        // header
        $blocks[] = [
            'slug' => 'testimonials-header',
            'url' => null,
            'is_active' => 1,
            'bg_image' => null,
            'icon' => null,
            'title' => 'Testimonials',
            'body' =>  null,
            'child' => [
                [
                    // Logo
                    'slug' => 'testimonials-header-logo',
                    'url' => null,
                    'is_active' => 1,


                    'bg_image' => null,
                    'icon' => null,
                    'title' => 'Logo',
                    'body' => null
                ],
                [
                    // Header Menu
                    'slug' => 'testimonials-header-menu',
                    'url' => null,
                    'is_active' => 1,


                    'bg_image' => null,
                    'icon' => null,
                    'title' => 'Header Menu',
                    'body' => json_encode([
                        [
                            'title' => 'Call Us',
                            'description' => '01128640295',
                            'link' => null,
                            'icon' => null
                        ],
                        [
                            'title' => 'Mail',
                            'description' => 'info@t3lam.com',
                            'link' => null,
                            'icon' => null
                        ]
                    ]),
                ]
            ]
        ];

        //Nav-bar
        $blocks[] = [
            'slug' => 'testimonials-nav-bar',
            'url' => null,
            'is_active' => 1,
            'bg_image' => null,
            'icon' => null,
            'title' => 'Navigation',
            'body' => json_encode([
                [
                    'title' => 'Home',
                    'description' => null,
                    'link' => null,
                    'icon' => null
                ],
                [
                    'title' => 'Testimonials',
                    'description' => null,
                    'link' => null,
                    'image' => null,
                    'icon' => null
                ],
                [
                    'title' => 'Video',
                    'description' => null,
                    'link' => null,
                    'icon' => null
                ]
            ]),
        ];



        $blocks[] = [
            'slug' => 'testimonials-body',
            'url' => null,
            'is_active' => 1,
            'bg_image' => null,
            'icon' => null,
            'title' => 'Testimonials',
            'body' => json_encode([
                [
                    'title' => 'image',
                    'description' => null,
                    'link' => null,
                    'icon' => null
                ],
                [
                    'title' => 'text technology',
                    'description' => null,
                    'link' => null,
                    'icon' => null
                ]
            ]),
        ];



        // footer
        $blocks[] = [
            'slug' => 'testimonials-footer',
            'url' => null,
            'is_active' => 1,
            'bg_image' => null,
            'icon' => null,
            'title' => 'Footer',
            'body' =>  null,
            'child' => [
                [
                    'slug' => 'testimonials-testimonials',
                    'url' => null,
                    'is_active' => 1,
                    'bg_image' => null,
                    'icon' => null,
                    'title' => 'Testimonials',
                    'body' =>  json_encode([
                        [
                            'title' => 'Testimonials',
                            'description' => null,
                            'link' => null,
                            'icon' => null
                        ],
                        [
                            'title' => 'Privacy',
                            'description' => null,
                            'link' => null,
                            'icon' => null
                        ]
                    ]),
                ]
            ]
        ];



        foreach ($blocks as $key => $block) {
            $block = Arr::add($block, 'page_id', $page->id);
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
                    $child = Arr::add($child, 'page_id', $page->id);
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
