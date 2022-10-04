<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use App\OurEdu\StaticBlocks\StaticBlock;
use App\OurEdu\StaticBlocks\StaticBlockTranslation;
use App\OurEdu\StaticPages\StaticPage;


class HomePageStaticData extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        dump('Home Page Seeder');
        $homepage = [
            'slug' => 'homepage',
            'url' => null,
            'bg_image' => null,
            'is_active' => 1,
            'title:en' => 'Ta3lom',
            'title:ar' => 'تعلم',
            'body:en' => null,
            'body:ar' => null
        ];

        $homepage =  StaticPage::create($homepage);

        // top header
        $blocks[] = [
            'slug' => 'homepage-top-header',
            'url' => null,
            'is_active' => 1,
            'bg_image' => null,
            'icon' => null,
            'title' => 'top header',
            'body' => null,
            'child' => [
                [
                    // Top header Logo
                    'slug' => 'homepage-top-header-logo',
                    'url' => null,
                    'is_active' => 1,
                    'bg_image' => null,
                    'icon' => null,
                    'title' => 'Logo',
                    'body' => null
                ],
                [
                    // Top Header Menu
                    'slug' => 'homepage-top-header-menu',
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
            'slug' => 'homepage-header',
            'url' => null,
            'is_active' => 1,
            'bg_image' => null,
            'icon' => null,
            'title' => 'Header',
            'body' =>  null,
            'child' => [
                [
                    // Logo
                    'slug' => 'homepage-header-logo',
                    'url' => null,
                    'is_active' => 1,


                    'bg_image' => null,
                    'icon' => null,
                    'title' => 'Logo',
                    'body' => null
                ],
                [
                    // Header Menu
                    'slug' => 'homepage-header-menu',
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
            'slug' => 'homepage-nav-bar',
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

        //Slider
        $blocks[] = [
            'slug' => 'homepage-slider',
            'url' => null,
            'is_active' => 1,
            'bg_image' => 'image',
            'icon' => null,
            'title' => 'Slider',
            'body' => json_encode([
                [
                    'title' => 'You Can Learn anything now',
                    'description' => null,
                    'link' => null,
                    'icon' => null
                ],
                [
                    'title' => 'Learn Mode',
                    'description' => null,
                    'link' => null,
                    'icon' => null
                ]
            ]),
        ];

        // VCR Instructors List
        $blocks[] = [
            'slug' => 'homepage-vcr-instructors',
            'url' => null,
            'is_active' => 1,
            'bg_image' => 'image',
            'icon' => null,
            'title' => 'VCR Instructors List',
            'body' => json_encode([
                [
                    'title' => 'Study with the best teachers',
                    'description' => null,
                    'link' => null,
                    'icon' => null
                ]
            ]),
        ];

        // Subjects
        $blocks[] = [
            'slug' => 'homepage-subjects',
            'url' => null,
            'is_active' => 1,
            'bg_image' => null,
            'icon' => null,
            'title' => 'Subjects',
            'body' => json_encode([
                [
                    'title' => 'Arabic',
                    'description' => null,
                    'link' => null,
                    'icon' => null
                ],
                [
                    'title' => 'Math',
                    'description' => null,
                    'link' => null,
                    'icon' => null
                ],
                [
                    'title' => 'Science',
                    'description' => null,
                    'link' => null,
                    'icon' => null
                ],
                [
                    'title' => 'Multiple',
                    'description' => null,
                    'link' => null,
                    'icon' => null
                ]
            ]),
        ];

        // Technology Cards
        $blocks[] = [
            'slug' => 'homepage-technology',
            'url' => null,
            'is_active' => 1,
            'bg_image' => null,
            'icon' => null,
            'title' => 'technology',
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

        // word of month
        $blocks[] = [
            'slug' => 'homepage-word-of-month',
            'url' => null,
            'is_active' => 1,
            'bg_image' => null,
            'icon' => null,
            'title' => 'word of month',
            'body' => json_encode([
                [
                    'title' => 'image',
                    'description' => null,
                    'link' => null,
                    'icon' => null
                ],
                [
                    'title' => 'word of month',
                    'description' => null,
                    'link' => null,
                    'icon' => null
                ]
            ]),
            'child' => [
                [
                    // Left Side Image
                    'slug' => 'homepage-word-of-month-image',
                    'url' => null,
                    'is_active' => 1,
                    'bg_image' => null,
                    'icon' => null,
                    'title' => 'Image',
                    'body' => json_encode([
                        [
                            'title' => 'image',
                            'alt' => 'image',
                            'link' => null,
                            'icon' => null
                        ],
                    ]),
                ],
                [
                    // Right Side Image
                    'slug' => 'homepage-word-of-month-texts',
                    'url' => null,
                    'is_active' => 1,
                    'bg_image' => null,
                    'icon' => null,
                    'title' => 'Word of month',
                    'body' => json_encode([
                        [
                            'title' => 'Word of month 1',
                            'description' => null,
                            'link' => null,
                            'icon' => null
                        ],
                        [
                            'title' => 'Word of month 2',
                            'description' => null,
                            'link' => null,
                            'icon' => null
                        ],
                        [
                            'title' => 'Word of month 3',
                            'description' => null,
                            'link' => null,
                            'icon' => null
                        ]
                    ]),
                ]
            ]
        ];

        // join us
        $blocks[] = [
            'slug' => 'homepage-join-us',
            'url' => null,
            'is_active' => 1,
            'bg_image' => null,
            'icon' => null,
            'title' => 'Join Us',
            'body' => json_encode([
                [
                    'title' => 'text',
                    'description' => null,
                    'link' => null,
                    'icon' => null
                ]
            ]),
        ];

        // how it works
        $blocks[] = [
            'slug' => 'homepage-how-it-works',
            'url' => null,
            'is_active' => 1,
            'bg_image' => null,
            'icon' => null,
            'title' => 'How it Works',
            'body' => json_encode([
                [
                    'title' => 'video',
                    'description' => null,
                    'link' => 'https://www.youtube.com/embed/LXb3EKWsInQ?autoplay=1',
                    'icon' => null
                ]
            ])
        ];

        // footer
        $blocks[] = [
            'slug' => 'homepage-footer',
            'url' => null,
            'is_active' => 1,
            'bg_image' => null,
            'icon' => null,
            'title' => 'Footer',
            'body' =>  null,
            'child' => [
                [
                    // About Us
                    'slug' => 'homepage-about-us',
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
                    'slug' => 'homepage-contact-us',
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
                    'slug' => 'homepage-social-media-links',
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
            $block = Arr::add($block, 'page_id', $homepage->id);
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
                    $child = Arr::add($child, 'page_id', $homepage->id);
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
