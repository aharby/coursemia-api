<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use App\OurEdu\StaticBlocks\StaticBlock;
use App\OurEdu\StaticBlocks\StaticBlockTranslation;
use App\OurEdu\StaticPages\StaticPage;


class AboutUsPageStaticData extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        dump('About Us Page Seeder');
        $aboutUsPage = [
            'slug' => 'about',
            'url' => null,
            'bg_image' => null,
            'is_active' => 1,
            'title:en' => 'About Us',
            'title:ar' => 'من نحن',
            'body:en' => null,
            'body:ar' => null
        ];

        $aboutUsPage =  StaticPage::create($aboutUsPage);

        // top header
        $blocks[] = [
            'slug' => 'about-top-header',
            'url' => null,
            'is_active' => 1,
            'bg_image' => null,
            'icon' => null,
            'title' => 'top header',
            'body' => null,
            'child' => [
                [
                    // Top header Logo
                    'slug' => 'about-top-header-logo',
                    'url' => null,
                    'is_active' => 1,
                    'bg_image' => null,
                    'icon' => null,
                    'title' => 'Logo',
                    'body' => null
                ],
                [
                    // Top Header Menu
                    'slug' => 'about-top-header-menu',
                    'url' => null,
                    'is_active' => 1,
                    'bg_image' => null,
                    'icon' => null,
                    'title' => 'Top Header Menu',
                    'body' => json_encode([
                        [
                            'title' => 'Become a teacher',
                            'description' => null,
                            'link' => null,
                            'icon' => null
                        ],
                        [
                            'title' => 'Log in',
                            'description' => null,
                            'link' => null,
                            'icon' => null
                        ],
                        [
                            'title' => 'Sign Up',
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
            'slug' => 'about-header',
            'url' => null,
            'is_active' => 1,
            'bg_image' => null,
            'icon' => null,
            'title' => 'Header',
            'body' =>  null,
            'child' => [
                [
                    // Logo
                    'slug' => 'about-header-logo',
                    'url' => null,
                    'is_active' => 1,


                    'bg_image' => null,
                    'icon' => null,
                    'title' => 'Logo',
                    'body' => null
                ],
                [
                    // Header Menu
                    'slug' => 'about-header-menu',
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
            'slug' => 'about-nav-bar',
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
                    'title' => 'About',
                    'description' => null,
                    'link' => null,
                    'image' => null,
                    'icon' => null
                ],
                [
                    'title' => 'Teachers',
                    'description' => null,
                    'link' => null,
                    'icon' => null
                ],
                [
                    'title' => 'News',
                    'description' => null,
                    'link' => null,
                    'icon' => null
                ],
                [
                    'title' => 'Video',
                    'description' => null,
                    'link' => null,
                    'icon' => null
                ],
                [
                    'title' => 'Testimonies',
                    'description' => null,
                    'link' => null,
                    'icon' => null
                ],
                [
                    'title' => 'Contact',
                    'description' => null,
                    'link' => null,
                    'icon' => null
                ]
            ]),
        ];



        // About body
        $blocks[] = [
            'slug' => 'about-body',
            'url' => null,
            'is_active' => 1,
            'bg_image' => null,
            'icon' => null,
            'title' => 'About Us',
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
            'slug' => 'about-footer',
            'url' => null,
            'is_active' => 1,
            'bg_image' => null,
            'icon' => null,
            'title' => 'Footer',
            'body' =>  null,
            'child' => [
                [
                    // About Us
                    'slug' => 'about-about-us',
                    'url' => null,
                    'is_active' => 1,
                    'bg_image' => null,
                    'icon' => null,
                    'title' => 'About Us',
                    'body' =>  json_encode([
                        [
                            'title' => 'Terms & Conditions',
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
                ],
                [
                    // Contact Us
                    'slug' => 'about-contact-us',
                    'url' => null,
                    'is_active' => 1,
                    'bg_image' => null,
                    'icon' => null,
                    'title' => 'Contact Us',
                    'body' => json_encode([
                        [
                            'title' => 'Need Help?',
                            'description' => null,
                            'link' => null,
                            'icon' => null
                        ],
                        [
                            'title' => 'Send Your Story',
                            'description' => null,
                            'link' => null,
                            'icon' => null
                        ],
                        [
                            'title' => 'Careers',
                            'description' => null,
                            'link' => null,
                            'icon' => null
                        ]
                    ]),
                ],
                [
                    // Social Links
                    'slug' => 'about-social-media-links',
                    'url' => null,
                    'is_active' => 1,
                    'bg_image' => null,
                    'icon' => null,
                    'title' => 'Social Links',
                    'body' => json_encode([
                        [
                            'title' => 'Facebook',
                            'description' => null,
                            'link' => null,
                            'icon' => null
                        ],
                        [
                            'title' => 'Twitter',
                            'description' => null,
                            'link' => null,
                            'icon' => null
                        ],
                        [
                            'title' => 'YouTube',
                            'description' => null,
                            'link' => null,
                            'icon' => null
                        ],
                        [
                            'title' => 'LinkedIn',
                            'description' => null,
                            'link' => null,
                            'icon' => null
                        ]
                    ]),
                ]
            ]
        ];



        foreach ($blocks as $key => $block) {
            $block = Arr::add($block, 'page_id', $aboutUsPage->id);
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
                    $child = Arr::add($child, 'page_id', $aboutUsPage->id);
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
