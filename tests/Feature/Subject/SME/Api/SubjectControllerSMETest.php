<?php

namespace Tests\Feature\Subject\SME\Api;

use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Countries\Country;
use App\OurEdu\EducationalSystems\EducationalSystem;
use App\OurEdu\GradeClasses\GradeClass;
use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\LearningResources\Enums\LearningResourcesPointsEnums;
use App\OurEdu\LearningResources\Resource;
use App\OurEdu\Options\Enums\OptionsTypes;
use App\OurEdu\Options\Option;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use App\OurEdu\Subjects\Models\SubModels\Task;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SubjectControllerSMETest extends TestCase
{
    use WithFaker;
    /**
     * A basic test example.
     *
     * @return void
     */


    public function test_update_subject_structural_first_time()
    {
        dump('test_update_subject_structural_first_time');
        $sme = $this->authSME();
        $this->apiSignIn($sme);

        $record = factory(Subject::class)->create();

        $record->update(['sme_id' => $sme->id]);

        $True_False_Resource = Resource::where('slug', LearningResourcesEnums::TRUE_FALSE)->first();
        $Video_Resource = Resource::where('slug', LearningResourcesEnums::Video)->first();
        $Audio_Resource = Resource::where('slug', LearningResourcesEnums::Audio)->first();
        $Multi_Choice_Resource = Resource::where('slug', LearningResourcesEnums::MULTI_CHOICE)->first();
        $dRAGDROPResource = Resource::where('slug', LearningResourcesEnums::DRAG_DROP)->first();
        $PDF_Resource = Resource::where('slug', LearningResourcesEnums::PDF)->first();
        $falshResource = Resource::where('slug', LearningResourcesEnums::FLASH)->first();
        $picture_Resource = Resource::where('slug', LearningResourcesEnums::PICTURE)->first();
        $complete_Resource = Resource::where('slug', LearningResourcesEnums::COMPLETE)->first();
        $matching_Resource = Resource::where('slug', LearningResourcesEnums::MATCHING)->first();
        $multiple_matching_Resource = Resource::where('slug', LearningResourcesEnums::MULTIPLE_MATCHING)->first();
        $hotspot_Resource = Resource::where('slug', LearningResourcesEnums::HOTSPOT)->first();


        $requestData = [
            "data" => [
                "type" => "subject",
                "id" => "{$record->id}",
                "attributes" => [
                    "section_type" => "section"
                ],
                "relationships" => [
                    "subjectFormatSubjects" => [
                        "data" => [
                            [
                                "type" => "subject_format_subject",
                                "id" => "new_1"
                            ],
                            [
                                "type" => "subject_format_subject",
                                "id" => "new_2"
                            ]
                        ]
                    ]
                ]
            ],
            "included" => [
                [
                    "type" => "subject_format_subject",
                    "id" => "new_1",
                    "attributes" => [
                        "title" => "section_1",
                        "subject_type" => "section",
                        "is_active" => true
                    ],
                    "relationships" => [
                        "subjectFormatSubjects" => [
                            "data" => [
                                [
                                    "type" => "subject_format_subject",
                                    "id" => "new_9"
                                ]
                            ]
                        ],
                        "resourceSubjectFormatSubjects" => [
                            "data" => [
                                [
                                    "type" => "resource_subject_format_subject",
                                    "id" => "new_1"
                                ],
                                [
                                    "type" => "resource_subject_format_subject",
                                    "id" => "new_2"
                                ],
                                [
                                    "type" => "resource_subject_format_subject",
                                    "id" => "new_3"
                                ],
                                [
                                    "type" => "resource_subject_format_subject",
                                    "id" => "new_4"
                                ],
                                [
                                    "type" => "resource_subject_format_subject",
                                    "id" => "new_5"
                                ],
                                [
                                    "type" => "resource_subject_format_subject",
                                    "id" => "new_6"
                                ],
                                [
                                    "type" => "resource_subject_format_subject",
                                    "id" => "new_7"
                                ],
                                [
                                    "type" => "resource_subject_format_subject",
                                    "id" => "new_8"
                                ],
                                [
                                    "type" => "resource_subject_format_subject",
                                    "id" => "new_9"

                                ],
                                [
                                    "type" => "resource_subject_format_subject",
                                    "id" => "new_10"
                                ],
                                [
                                    "type" => "resource_subject_format_subject",
                                    "id" => "new_11"
                                ],
                                [
                                    "type" => "resource_subject_format_subject",
                                    "id" => "new_12"
                                ],
                            ]
                        ]
                    ]
                ],
                [
                    "type" => "subject_format_subject",
                    "id" => "new_9",
                    "attributes" => [
                        "title" => "section_9",
                        "subject_type" => "section",
                        "is_active" => true
                    ]
                ],
                [
                    "type" => "subject_format_subject",
                    "id" => "new_2",
                    "attributes" => [
                        "title" => "section_2",
                        "subject_type" => "section",
                        "is_active" => true
                    ]
                ],
                [
                    "type" => "resource_subject_format_subject",
                    "id" => "new_1",
                    "attributes" => [
                        "resource_id" => "{$Video_Resource->id}",
                        "resource_slug" => "video",
                        "is_active" => true
                    ],
                    "relationships" => [
                        "learningResourceAcceptCriteria" => [
                            "data" => [

                                "type" => "AcceptCriteria",
                                "id" => "bf05eb7e-45a2-4238-a316-6863767b6bc1"

                            ]
                        ]
                    ]
                ],
                [
                    "type" => "resource_subject_format_subject",
                    "id" => "new_2",
                    "attributes" => [
                        "resource_id" => "{$True_False_Resource->id}",
                        "resource_slug" => "true_false",
                        "is_active" => true
                    ],
                    "relationships" => [
                        "learningResourceAcceptCriteria" => [
                            "data" => [
                                "type" => "AcceptCriteria",
                                "id" => "bf05eb7e-45a2-4238-a316-6863767b6bc2"
                            ]
                        ]
                    ]
                ],

                [
                    "type" => "resource_subject_format_subject",
                    "id" => "new_3",
                    "attributes" => [
                        "resource_id" => "{$Audio_Resource->id}",
                        "resource_slug" => "audio",
                        "is_active" => true
                    ],
                    "relationships" => [
                        "learningResourceAcceptCriteria" => [
                            "data" => [

                                "type" => "AcceptCriteria",
                                "id" => "bf05eb7e-45a2-4238-a316-6863767b6bc3"

                            ]
                        ]
                    ]
                ],
                [
                    "type" => "resource_subject_format_subject",
                    "id" => "new_4",
                    "attributes" => [
                        "resource_id" => "{$Multi_Choice_Resource->id}",
                        "resource_slug" => "multi_choice",
                        "is_active" => true
                    ],
                    "relationships" => [
                        "learningResourceAcceptCriteria" => [
                            "data" => [

                                "type" => "AcceptCriteria",
                                "id" => "bf05eb7e-45a2-4238-a316-6863767b6bc4"

                            ]
                        ]
                    ]
                ],
                [
                    "type" => "resource_subject_format_subject",
                    "id" => "new_5",
                    "attributes" => [
                        "resource_id" => "{$dRAGDROPResource->id}",
                        "resource_slug" => "drag_drop",
                        "is_active" => true
                    ],
                    "relationships" => [
                        "learningResourceAcceptCriteria" => [
                            "data" => [

                                "type" => "AcceptCriteria",
                                "id" => "48de6f75-994c-4b10-ae5c-173759c8c198"

                            ]
                        ]
                    ]
                ],
                // PDF resource
                [
                    "type" => "resource_subject_format_subject",
                    "id" => "new_6",
                    "attributes" => [
                        "resource_id" => "{$PDF_Resource->id}",
                        "resource_slug" => "pdf",
                        "is_active" => true
                    ],
                    "relationships" => [
                        "learningResourceAcceptCriteria" => [
                            "data" => [

                                "type" => "AcceptCriteria",
                                "id" => "69f1034f-dfaa-44d4-ab56-d6e9ca144bee"

                            ]
                        ]
                    ]
                ],
                [
                    "type" => "resource_subject_format_subject",
                    "id" => "new_7",
                    "attributes" => [
                        "resource_id" => "{$falshResource->id}",
                        "resource_slug" => "flash",
                        "is_active" => true
                    ],
                    "relationships" => [
                        "learningResourceAcceptCriteria" => [
                            "data" => [

                                "type" => "AcceptCriteria",
                                "id" => "48de6f75-994c-4b10-ae5c-173740c8c198"

                            ]
                        ]
                    ]
                ],
                // Picture resource
                [
                    "type" => "resource_subject_format_subject",
                    "id" => "new_8",
                    "attributes" => [
                        "resource_id" => "{$picture_Resource->id}",
                        "resource_slug" => "picture",
                        "is_active" => true
                    ],
                    "relationships" => [
                        "learningResourceAcceptCriteria" => [
                            "data" => [

                                "type" => "AcceptCriteria",
                                "id" => "69f1034f-dfaa-44d4-ab56-d6e9ca144be7"

                            ]
                        ]
                    ]
                ],
                // Complete resource
                [
                    "type" => "resource_subject_format_subject",
                    "id" => "new_11",
                    "attributes" => [
                        "resource_id" => "{$complete_Resource->id}",
                        "resource_slug" => "complete",
                        "is_active" => true
                    ],
                    "relationships" => [
                        "learningResourceAcceptCriteria" => [
                            "data" => [

                                "type" => "AcceptCriteria",
                                "id" => "69f1034f-dfaa-44d4-ab56-d6e9ca16985s"

                            ]
                        ]
                    ]
                ],
                // Matching resource
                [
                    "type" => "resource_subject_format_subject",
                    "id" => "new_9",
                    "attributes" => [
                        "resource_id" => "{$matching_Resource->id}",
                        "resource_slug" => LearningResourcesEnums::MATCHING,
                        "is_active" => true
                    ],
                    "relationships" => [
                        "learningResourceAcceptCriteria" => [
                            "data" => [

                                    "type" => "AcceptCriteria",
                                    "id" => "69f1034f-dfaa-44d4-asd56-d6e9ca144be7"

                            ]
                        ]
                    ]
                ],
                // Multi Matching resource
                [
                    "type" => "resource_subject_format_subject",
                    "id" => "new_10",
                    "attributes" => [
                        "resource_id" => "{$multiple_matching_Resource->id}",
                        "resource_slug" => LearningResourcesEnums::MULTIPLE_MATCHING,
                        "is_active" => true
                    ],
                    "relationships" => [
                        "learningResourceAcceptCriteria" => [
                            "data" => [

                                    "type" => "AcceptCriteria",
                                    "id" => "69f1034f-dfaa-44d4-aasd56-d6e9ca144be7"

                            ]
                        ]
                    ]
                ],
                // HOTSPOT resource
                [
                    "type" => "resource_subject_format_subject",
                    "id" => "new_12",
                    "attributes" => [
                        "resource_id" => "{$hotspot_Resource->id}",
                        "resource_slug" => LearningResourcesEnums::HOTSPOT,
                        "is_active" => true
                    ],
                    "relationships" => [
                        "learningResourceAcceptCriteria" => [
                            "data" => [

                                    "type" => "AcceptCriteria",
                                    "id" => "69f1034f-dfaa-44d4-aasd56-adekca144be7"

                            ]
                        ]
                    ]
                ],
                [
                    "type" => "AcceptCriteria",
                    "id" => "bf05eb7e-45a2-4238-a316-6863767b6bc1",
                    "attributes" => [
                        "due_date" => 4,
                        "description" => "Testt",
                        "video_type" => "1",

                    ]
                ],
                [
                    "type" => "AcceptCriteria",
                    "id" => "bf05eb7e-45a2-4238-a316-6863767b6bc2",
                    "attributes" => [
                        "due_date" => 4,
                        "description" => "optional string",
                        "number_of_question" => 5,
                        "difficulty_level" => 38,
                        "learning_outcome" => 39,
                        "true_false_type" => 41
                    ]
                ],
                [
                    "type" => "AcceptCriteria",
                    "id" => "bf05eb7e-45a2-4238-a316-6863767b6bc3",
                    "attributes" => [
                        "due_date" => 4,
                        "description" => "Testt",
                        "audio_type" => "1",
                        "number_of_question" => 5,
                        "difficulty_level" => 38,
                        "learning_outcome" => 2,


                    ]
                ],
                [
                    "type" => "AcceptCriteria",
                    "id" => "bf05eb7e-45a2-4238-a316-6863767b6bc4",
                    "attributes" => [
                        "due_date" => 1,
                        "description" => "sda",
                        "number_of_question" => 5,
                        "difficulty_level" => 1,
                        "learning_outcome" => 2,
                        "multiple_choice" => 3,

                    ]
                ],
                [
                    "type" => "AcceptCriteria",
                    "id" => "48de6f75-994c-4b10-ae5c-173740c8c198",
                    "attributes" => [
                        "due_date" => 4,
                        "description" => "flash",
                        "flash" => 'file',
                    ]
                ],
                [
                    "type" => "AcceptCriteria",
                    "id" => "48de6f75-994c-4b10-ae5c-173759c8c198",
                    "attributes" => [
                        "due_date" => 1,
                        "description" => "sda",
                        "number_of_question" => 5,
                        "difficulty_level" => "1",
                        "learning_outcome" => "2",
                        "drag_drop_type" => "5",
                    ]
                ],
                [   // PDF Acceptance criteria
                    "type" => "AcceptCriteria",
                    "id" => "69f1034f-dfaa-44d4-ab56-d6e9ca144bee",
                    "attributes" => [
                        "due_date" => 4,
                        "description" => "PDF test",
                        "pdf_type" => "URL"
                    ]
                ],
                [   // Picture Acceptance criteria
                    "type" => "AcceptCriteria",
                    "id" => "69f1034f-dfaa-44d4-ab56-d6e9ca144be7",
                    "attributes" => [
                        "due_date" => 4,
                        "description" => "Picture test",
                        "picture_type" => "URL"
                    ]
                ],
                [   // Complete Acceptance criteria
                    "type" => "AcceptCriteria",
                    "id" => "69f1034f-dfaa-44d4-ab56-d6e9ca16985s",
                    "attributes" => [
                        "due_date" => 4,
                        "description" => "complete",
                        "number_of_question" => "5",
                        "difficulty_level" => "11",
                        "learning_outcome" => "10"
                    ]
                ],
                [   // Matching Acceptance criteria
                    "type" => "AcceptCriteria",
                    "id" => "69f1034f-dfaa-44d4-asd56-d6e9ca144be7",
                    "attributes" => [
                        "due_date" => 4,
                        "description" => "Matching test",
                        "difficulty_level" => "1",
                        "learning_outcome" => "2",
                        "number_of_question" => 2
                    ],
                ],
                [   // Multi Matching Acceptance criteria
                    "type" => "AcceptCriteria",
                    "id" => "69f1034f-dfaa-44d4-aasd56-d6e9ca144be7",
                    "attributes" => [
                        "due_date" => 4,
                        "description" => "Multi Matching test",
                        "difficulty_level" => "1",
                        "learning_outcome" => "2",
                        "number_of_question" => 2
                    ]
                ],
                [   // HotSpot Acceptance criteria
                    "type" => "AcceptCriteria",
                    "id" => "69f1034f-dfaa-44d4-aasd56-adekca144be7",
                    "attributes" => [
                        "description" => "HotSpot test",
                        "difficulty_level" => "1",
                        "learning_outcome" => "2",
                        "number_of_question" => 2
                    ]
                ],
            ]
        ];


        $response = $this->putJson("/api/v1/en/sme/subjects/{$record->id}/structural", $requestData)->assertOk();


        $section1 = SubjectFormatSubject::where('title', 'section_1')->first();

        $section2 = SubjectFormatSubject::where('title', 'section_2')->first();

        $section9 = SubjectFormatSubject::where('title', 'section_9')->first();
        $this->assertDatabaseHas('subject_format_subject', [
            'title' => 'section_1',
            'is_active' => 1,
            'parent_subject_format_id' => null

        ]);

        $this->assertDatabaseHas('subject_format_subject', [
            'title' => 'section_2',
            'is_active' => 1,
            'parent_subject_format_id' => null

        ]);

        $this->assertDatabaseHas('subject_format_subject', [
            'title' => 'section_9',
            'is_active' => 1,
            'parent_subject_format_id' => $section1->id

        ]);

        $this->assertDatabaseHas('resource_subject_format_subject', [
            'resource_id' => $Video_Resource->id,
            'subject_format_subject_id' => $section1->id,
            'is_active' => 1,
            'accept_criteria' => '{"due_date":4,"description":"Testt","video_type":"1"}',


        ]);

        $this->assertDatabaseHas('resource_subject_format_subject', [
            'resource_id' => $True_False_Resource->id,
            'subject_format_subject_id' => $section1->id,
            'is_active' => 1,
            'accept_criteria' => "{\"due_date\":4,\"description\":\"optional string\",\"number_of_question\":5,\"difficulty_level\":38,\"learning_outcome\":39,\"true_false_type\":41}",


        ]);

        $this->assertDatabaseHas('resource_subject_format_subject', [
            'resource_id' => $Audio_Resource->id,
            'subject_format_subject_id' => $section1->id,
            'is_active' => 1,
            'accept_criteria' => "{\"due_date\":4,\"description\":\"Testt\",\"audio_type\":\"1\",\"number_of_question\":5,\"difficulty_level\":38,\"learning_outcome\":2}",


        ]);

        $this->assertDatabaseHas('resource_subject_format_subject', [
            'resource_id' => $complete_Resource->id,
            'subject_format_subject_id' => $section1->id,
            'is_active' => 1,
            'accept_criteria' => "{\"due_date\":4,\"description\":\"complete\",\"number_of_question\":\"5\",\"difficulty_level\":\"11\",\"learning_outcome\":\"10\"}",


        ]);

        $this->assertDatabaseHas('resource_subject_format_subject', [
            'resource_id' => $multiple_matching_Resource->id,
            'subject_format_subject_id' => $section1->id,
            'is_active' => 1,
            'accept_criteria' => '{"due_date":4,"description":"Multi Matching test","difficulty_level":"1","learning_outcome":"2","number_of_question":2}',


        ]);


        $this->assertDatabaseHas('resource_subject_format_subject', [
            'resource_id' => $dRAGDROPResource->id,
            'subject_format_subject_id' => $section1->id,
            'is_active' => 1,
            'accept_criteria' => '{"due_date":1,"description":"sda","number_of_question":5,"difficulty_level":"1","learning_outcome":"2","drag_drop_type":"5"}',


        ]);


        // PDF testing
        $this->assertDatabaseHas('resource_subject_format_subject', [
            'resource_id' => $PDF_Resource->id,
            'subject_format_subject_id' => $section1->id,
            'is_active' => 1,
            'accept_criteria' => '{"due_date":4,"description":"PDF test","pdf_type":"URL"}',
        ]);


        $this->assertDatabaseHas('resource_subject_format_subject', [
            'resource_id' => $falshResource->id,
            'subject_format_subject_id' => $section1->id,
            'is_active' => 1,
            'accept_criteria' => '{"due_date":4,"description":"flash","flash":"file"}',
        ]);

        // Picture testing
        $this->assertDatabaseHas('resource_subject_format_subject', [
            'resource_id' => $picture_Resource->id,
            'subject_format_subject_id' => $section1->id,
            'is_active' => 1,
            'accept_criteria' => '{"due_date":4,"description":"Picture test","picture_type":"URL"}',
        ]);

        // Matching testing
        $this->assertDatabaseHas('resource_subject_format_subject', [
            'resource_id' => $matching_Resource->id,
            'subject_format_subject_id' => $section1->id,
            'is_active' => 1,
            'accept_criteria' => '{"due_date":4,"description":"Matching test","difficulty_level":"1","learning_outcome":"2","number_of_question":2}',

        ]);


        // Multiple Matching testing
        $this->assertDatabaseHas('resource_subject_format_subject', [
            'resource_id' => $multiple_matching_Resource->id,
            'subject_format_subject_id' => $section1->id,
            'is_active' => 1,
            'accept_criteria' => '{"due_date":4,"description":"Multi Matching test","difficulty_level":"1","learning_outcome":"2","number_of_question":2}',
        ]);

        // hotspot testing
        $this->assertDatabaseHas('resource_subject_format_subject', [
            'resource_id' => $hotspot_Resource->id,
            'subject_format_subject_id' => $section1->id,
            'is_active' => 1,
            'accept_criteria' => '{"description":"HotSpot test","difficulty_level":"1","learning_outcome":"2","number_of_question":2}',
        ]);


//            ->assertJsonFragment(['message'   =>  trans('api.Password changed')]);
    }

    public function test_update_subject_structural()
    {
        dump('test_update_subject_structural');
        $sme = $this->authSME();
        $this->apiSignIn($sme);

        $record = factory(Subject::class)->create();

        $record->update(['sme_id' => $sme->id]);

        $True_False_Resource = Resource::where('slug', LearningResourcesEnums::TRUE_FALSE)->first();
        $Multi_Choice_Resource = Resource::where('slug', LearningResourcesEnums::MULTI_CHOICE)->first();
        $requestData = [
            "data" => [
                "type" => "subject",
                "id" => "{$record->id}",
                "attributes" => [
                    "section_type" => "section"
                ],
                "relationships" => [
                    "subjectFormatSubjects" => [
                        "data" => [
                            [
                                "type" => "subject_format_subject",
                                "id" => "new_1"
                            ],
                            [
                                "type" => "subject_format_subject",
                                "id" => "new_2"
                            ]
                        ]
                    ]
                ]
            ],
            "included" => [
                [
                    "type" => "subject_format_subject",
                    "id" => "new_1",
                    "attributes" => [
                        "title" => "section_1",
                        "subject_type" => "section",
                        "is_active" => true
                    ],
                    "relationships" => [
                        "subjectFormatSubjects" => [
                            "data" => [
                                [
                                    "type" => "subject_format_subject",
                                    "id" => "new_9"
                                ]
                            ]
                        ],
                        "resourceSubjectFormatSubjects" => [
                            "data" => [
                                [
                                    "type" => "resource_subject_format_subject",
                                    "id" => "new_1"
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    "type" => "subject_format_subject",
                    "id" => "new_9",
                    "attributes" => [
                        "title" => "section_9",
                        "subject_type" => "section",
                        "is_active" => true
                    ]
                ],
                [
                    "type" => "subject_format_subject",
                    "id" => "new_2",
                    "attributes" => [
                        "title" => "section_2",
                        "subject_type" => "section",
                        "is_active" => true
                    ]
                ],
                [
                    "type" => "resource_subject_format_subject",
                    "id" => "new_1",
                    "attributes" => [
                        "resource_id" => "{$True_False_Resource->id}",
                        "slug" => "true_false",
                        "is_active" => true
                    ],
                    "relationships" => [
                        "learningResourceAcceptCriteria" => [
                            "data" => [

                                "type" => "AcceptCriteria",
                                "id" => "bf05eb7e-45a2-4238-a316-6863767b6bc1"

                            ]
                        ]
                    ]
                ],
                [
                    "type" => "AcceptCriteria",
                    "id" => "bf05eb7e-45a2-4238-a316-6863767b6bc1",
                    "attributes" => [
                        "due_date" => 4,
                        "description" => "optional string",
                        "number_of_question" => 5,
                        "difficulty_level" => 38,
                        "learning_outcome" => 39,
                        "true_false_type" => 41
                    ]
                ]
            ]
        ];

        $response = $this->putJson("/api/v1/en/sme/subjects/{$record->id}/structural", $requestData)
            ->assertStatus(200);
        $section1 = SubjectFormatSubject::where('title', 'section_1')->first();

        $section2 = SubjectFormatSubject::where('title', 'section_2')->first();

        $section9 = SubjectFormatSubject::where('title', 'section_9')->first();


        $requestDataUpdate = [
            "data" => [
                "type" => "subject",
                "id" => "{$record->id}",
                "attributes" => [
                    "section_type" => "section"
                ],
                "relationships" => [
                    "subjectFormatSubjects" => [
                        "data" => [
                            [
                                "type" => "subject_format_subject",
                                "id" => "{$section1->id}"
                            ],
                            [
                                "type" => "subject_format_subject",
                                "id" => "new_3"
                            ]
                        ]
                    ]
                ]
            ],
            "included" => [
                [
                    "type" => "subject_format_subject",
                    "id" => "{$section1->id}",
                    "attributes" => [
                        "title" => "new_section_1",
                        "subject_type" => "section",
                        "is_active" => true
                    ],
                    "relationships" => [
                        "subjectFormatSubjects" => [
                            "data" => [
                                [
                                    "type" => "subject_format_subject",
                                    "id" => "new_9"
                                ]
                            ]
                        ],
                        "resourceSubjectFormatSubjects" => [
                            "data" => [
                                [
                                    "type" => "resource_subject_format_subject",
                                    "id" => "new_1"
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    "type" => "subject_format_subject",
                    "id" => "new_3",
                    "attributes" => [
                        "title" => "new_section_3",
                        "subject_type" => "section",
                        "is_active" => true
                    ]
                ],
                [
                    "type" => "subject_format_subject",
                    "id" => "new_9",
                    "attributes" => [
                        "title" => "new_section_9",
                        "subject_type" => "section",
                        "is_active" => true
                    ]
                ],
                [
                    "type" => "resource_subject_format_subject",
                    "id" => "new_1",
                    "attributes" => [
                        "resource_id" => "{$True_False_Resource->id}",
                        "slug" => "true_false",
                        "is_active" => true
                    ],
                    "relationships" => [
                        "learningResourceAcceptCriteria" => [
                            "data" => [

                                "type" => "AcceptCriteria",
                                "id" => "bf05eb7e-45a2-4238-a316-6863767b6bc1"

                            ]
                        ]
                    ]
                ],
                [
                    "type" => "AcceptCriteria",
                    "id" => "bf05eb7e-45a2-4238-a316-6863767b6bc1",
                    "attributes" => [
                        "due_date" => 4,
                        "description" => "optional string",
                        "number_of_question" => 5,
                        "difficulty_level" => 38,
                        "learning_outcome" => 39,
                        "true_false_type" => 41
                    ]
                ]
            ]
        ];

        $response = $this->putJson("/api/v1/en/sme/subjects/{$record->id}/structural", $requestDataUpdate)
            ->assertStatus(200);

        $this->assertDatabaseHas('subject_format_subject', [
            'title' => 'new_section_1',
            'is_active' => 1,
            'parent_subject_format_id' => null

        ]);

        $this->assertDatabaseMissing('subject_format_subject', [
            'title' => 'section_22',
            'is_active' => 1,
            'parent_subject_format_id' => null

        ]);

        $this->assertDatabaseHas('subject_format_subject', [
            'title' => 'new_section_9',
            'is_active' => 1,
            'parent_subject_format_id' => $section1->id

        ]);

        $this->assertDatabaseHas('resource_subject_format_subject', [
            'resource_id' => $True_False_Resource->id,
            'subject_format_subject_id' => $section1->id,
            'is_active' => 1,
            'accept_criteria' => "{\"due_date\":4,\"description\":\"optional string\",\"number_of_question\":5,\"difficulty_level\":38,\"learning_outcome\":39,\"true_false_type\":41}",

        ]);
    }

    public function test_generate_task()
    {
        dump('test_generate_task');
        $sme = $this->authSME();
        $this->apiSignIn($sme);

        $record = factory(Subject::class)->create();

        $record->update(['sme_id' => $sme->id]);

        $True_False_Resource = Resource::where('slug', LearningResourcesEnums::TRUE_FALSE)->first();
        $Multi_Choice_Resource = Resource::where('slug', LearningResourcesEnums::MULTI_CHOICE)->first();
        $requestData = [
            "data" => [
                "type" => "subject",
                "id" => "{$record->id}",
                "attributes" => [
                    "section_type" => "section"
                ],
                "relationships" => [
                    "subjectFormatSubjects" => [
                        "data" => [
                            [
                                "type" => "subject_format_subject",
                                "id" => "new_1"
                            ],
                            [
                                "type" => "subject_format_subject",
                                "id" => "new_2"
                            ]
                        ]
                    ]
                ]
            ],
            "included" => [
                [
                    "type" => "subject_format_subject",
                    "id" => "new_1",
                    "attributes" => [
                        "title" => "section_1",
                        "subject_type" => "section",
                        "is_active" => true
                    ],
                    "relationships" => [
                        "subjectFormatSubjects" => [
                            "data" => [
                                [
                                    "type" => "subject_format_subject",
                                    "id" => "new_9"
                                ]
                            ]
                        ],
                        "resourceSubjectFormatSubjects" => [
                            "data" => [
                                [
                                    "type" => "resource_subject_format_subject",
                                    "id" => "new_1"
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    "type" => "subject_format_subject",
                    "id" => "new_9",
                    "attributes" => [
                        "title" => "section_9",
                        "subject_type" => "section",
                        "is_active" => true
                    ]
                ],
                [
                    "type" => "subject_format_subject",
                    "id" => "new_2",
                    "attributes" => [
                        "title" => "section_2",
                        "subject_type" => "section",
                        "is_active" => true
                    ]
                ],
                [
                    "type" => "resource_subject_format_subject",
                    "id" => "new_1",
                    "attributes" => [
                        "resource_id" => "{$True_False_Resource->id}",
                        "resource_slug" => "video",
                        "is_active" => true
                    ],
                    "relationships" => [
                        "learningResourceAcceptCriteria" => [
                            "data" =>
                                [
                                    "type" => "AcceptCriteria",
                                    "id" => "bf05eb7e-45a2-4238-a316-6863767b6bc1"
                                ]

                        ]
                    ]
                ],
                [
                    "type" => "AcceptCriteria",
                    "id" => "bf05eb7e-45a2-4238-a316-6863767b6bc1",
                    "attributes" => [
                        "due_date" => 4,
                        "number_of_question" => 5,
                        "video_type" => "1",

                    ]
                ]
            ]
        ];

        $response = $this->putJson("/api/v1/en/sme/subjects/{$record->id}/structural?is_generate=true", $requestData)
            ->assertStatus(200);

        $section1 = SubjectFormatSubject::where('title', 'section_1')->first();

        $section2 = SubjectFormatSubject::where('title', 'section_2')->first();

        $section9 = SubjectFormatSubject::where('title', 'section_9')->first();

        $resourceSubjectFormatSubject = $section1->resourceSubjectFormatSubject()->first();


        $this->assertDatabaseHas('subject_format_subject', [
            'title' => 'section_1',
            'is_active' => 1,
            'parent_subject_format_id' => null

        ]);


        $this->assertDatabaseHas('resource_subject_format_subject', [
            'resource_id' => $True_False_Resource->id,
            'subject_format_subject_id' => $section1->id,
            'is_active' => 1,


        ]);


        $taskData = [
            'due_date' => 4,
            'is_active' => 1,
            'subject_id' => $record->id,
            'resource_subject_format_subject_id' => $resourceSubjectFormatSubject->id,
            'subject_format_subject_id' => $section1->id,


        ];
        $this->assertDatabaseHas('tasks', $taskData);

        $task = Task::where($taskData)->first();

        $this->assertNotEmpty($task->title);
    }

    public function test_sme_pause_unpause_subject()
    {
        dump('test_sme_pause_unpause_subject');
        $sme = $this->authSME();
        $this->apiSignIn($sme);

        $record = factory(Subject::class)->create();
        $record->update(['sme_id' => $sme->id]);


        //test pause subject
        $record->update(['is_active' => 1]);

        $response = $this->post("/api/v1/en/sme/subjects/{$record->id}/pause-unpause")
            ->assertStatus(200);


        $this->assertDatabaseHas('subjects', [
            'id' => $record->id,
            'is_active' => 0,
        ]);


        //test unpause subject
        $record->update(['is_active' => 0]);

        $response = $this->post("/api/v1/en/sme/subjects/{$record->id}/pause-unpause")
            ->assertStatus(200);


        $this->assertDatabaseHas('subjects', [
            'id' => $record->id,
            'is_active' => 1,
        ]);
    }

    public function test_get_clone_subject()
    {
        dump('test_get_clone_subject');
        $sme = $this->authSME();
        $this->apiSignIn($sme);

        $record = factory(Subject::class)->create();
        $record->update([
            'sme_id' => $sme->id,
        ]);

        $True_False_Resource = Resource::where('slug', LearningResourcesEnums::TRUE_FALSE)->first();
        $Multi_Choice_Resource = Resource::where('slug', LearningResourcesEnums::MULTI_CHOICE)->first();
        $section1 = SubjectFormatSubject::where('title', 'section_1')->first();
        $section2 = SubjectFormatSubject::where('title', 'section_2')->first();
        $section9 = SubjectFormatSubject::where('title', 'section_9')->first();

        $requestDataUpdate = [
            "data" => [
                "type" => "subject",
                "id" => "{$record->id}",
                "attributes" => [
                    "section_type" => "section"
                ],
                "relationships" => [
                    "subjectFormatSubjects" => [
                        "data" => [
                            [
                                "type" => "subject_format_subject",
                                "id" => "{$section1->id}"
                            ],
                            [
                                "type" => "subject_format_subject",
                                "id" => "new_3"
                            ]
                        ]
                    ]
                ]
            ],
            "included" => [
                [
                    "type" => "subject_format_subject",
                    "id" => "{$section1->id}",
                    "attributes" => [
                        "title" => "new_section_1",
                        "subject_type" => "section",
                        "is_active" => true
                    ],
                    "relationships" => [
                        "subjectFormatSubjects" => [
                            "data" => [
                                [
                                    "type" => "subject_format_subject",
                                    "id" => "new_9"
                                ]
                            ]
                        ],
                        "resourceSubjectFormatSubjects" => [
                            "data" => [
                                [
                                    "type" => "resource_subject_format_subject",
                                    "id" => "new_1"
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    "type" => "subject_format_subject",
                    "id" => "new_3",
                    "attributes" => [
                        "title" => "new_section_3",
                        "subject_type" => "section",
                        "is_active" => true
                    ]
                ],
                [
                    "type" => "subject_format_subject",
                    "id" => "new_9",
                    "attributes" => [
                        "title" => "new_section_9",
                        "subject_type" => "section",
                        "is_active" => true
                    ]
                ],
                [
                    "type" => "resource_subject_format_subject",
                    "id" => "new_1",
                    "attributes" => [
                        "resource_id" => "{$True_False_Resource->id}",
                        "slug" => "true_false",
                        "is_active" => true
                    ],
                    "relationships" => [
                        "learningResourceAcceptCriteria" => [
                            "data" => [

                                "type" => "AcceptCriteria",
                                "id" => "bf05eb7e-45a2-4238-a316-6863767b6bc1"

                            ]
                        ]
                    ]
                ],
                [
                    "type" => "AcceptCriteria",
                    "id" => "bf05eb7e-45a2-4238-a316-6863767b6bc1",
                    "attributes" => [
                        "due_date" => 4,
                        "description" => "optional string",
                        "number_of_question" => 5,
                        "difficulty_level" => 38,
                        "learning_outcome" => 39,
                        "true_false_type" => 41
                    ]
                ]
            ]
        ];
        // update the subject structure
        $this->putJson("/api/v1/en/sme/subjects/{$record->id}/structural", $requestDataUpdate)
            ->assertStatus(200);

        // get clone the subject
        $this->getJson("/api/v1/en/sme/subjects/clone-subject/{$record->id}")
            ->assertStatus(200);
    }

    public function test_update_subject_structural_with_points()
    {
        dump('test_update_subject_structural_with_points');
        $sme = $this->authSME();
        $this->apiSignIn($sme);

        $record = factory(Subject::class)->create();

        $record->update(['sme_id' => $sme->id]);

        $Video_Resource = Resource::where('slug', LearningResourcesEnums::Video)->first();
        $Audio_Resource = Resource::where('slug', LearningResourcesEnums::Audio)->first();
        $PDF_Resource = Resource::where('slug', LearningResourcesEnums::PDF)->first();
        $falshResource = Resource::where('slug', LearningResourcesEnums::FLASH)->first();


        $requestData = [
            "data" => [
                "type" => "subject",
                "id" => "{$record->id}",
                "attributes" => [
                    "section_type" => "section"
                ],
                "relationships" => [
                    "subjectFormatSubjects" => [
                        "data" => [
                            [
                                "type" => "subject_format_subject",
                                "id" => "new_1"
                            ],
                            [
                                "type" => "subject_format_subject",
                                "id" => "new_2"
                            ]
                        ]
                    ]
                ]
            ],
            "included" => [
                [
                    "type" => "subject_format_subject",
                    "id" => "new_1",
                    "attributes" => [
                        "title" => "section_1",
                        "subject_type" => "section",
                        "is_active" => true
                    ],
                    "relationships" => [
                        "subjectFormatSubjects" => [
                            "data" => [
                                [
                                    "type" => "subject_format_subject",
                                    "id" => "new_9"
                                ]
                            ]
                        ],
                        "resourceSubjectFormatSubjects" => [
                            "data" => [
                                [
                                    "type" => "resource_subject_format_subject",
                                    "id" => "new_1"
                                ],
                                [
                                    "type" => "resource_subject_format_subject",
                                    "id" => "new_3"
                                ],
                                [
                                    "type" => "resource_subject_format_subject",
                                    "id" => "new_6"
                                ],
                                [
                                    "type" => "resource_subject_format_subject",
                                    "id" => "new_7"
                                ],
                            ]
                        ]
                    ]
                ],
                [
                    "type" => "subject_format_subject",
                    "id" => "new_9",
                    "attributes" => [
                        "title" => "section_9",
                        "subject_type" => "section",
                        "is_active" => true
                    ]
                ],
                [
                    "type" => "subject_format_subject",
                    "id" => "new_2",
                    "attributes" => [
                        "title" => "section_2",
                        "subject_type" => "section",
                        "is_active" => true
                    ]
                ],
                [
                    "type" => "resource_subject_format_subject",
                    "id" => "new_1",
                    "attributes" => [
                        "resource_id" => "{$Video_Resource->id}",
                        "resource_slug" => "video",
                        "is_active" => true
                    ],
                    "relationships" => [
                        "learningResourceAcceptCriteria" => [
                            "data" => [

                                "type" => "AcceptCriteria",
                                "id" => "bf05eb7e-45a2-4238-a316-6863767b6bc1"

                            ]
                        ]
                    ]
                ],
                [
                    "type" => "resource_subject_format_subject",
                    "id" => "new_3",
                    "attributes" => [
                        "resource_id" => "{$Audio_Resource->id}",
                        "resource_slug" => "audio",
                        "is_active" => true
                    ],
                    "relationships" => [
                        "learningResourceAcceptCriteria" => [
                            "data" => [

                                "type" => "AcceptCriteria",
                                "id" => "bf05eb7e-45a2-4238-a316-6863767b6bc3"

                            ]
                        ]
                    ]
                ],
                // PDF resource
                [
                    "type" => "resource_subject_format_subject",
                    "id" => "new_6",
                    "attributes" => [
                        "resource_id" => "{$PDF_Resource->id}",
                        "resource_slug" => "pdf",
                        "is_active" => true
                    ],
                    "relationships" => [
                        "learningResourceAcceptCriteria" => [
                            "data" => [

                                "type" => "AcceptCriteria",
                                "id" => "69f1034f-dfaa-44d4-ab56-d6e9ca144bee"

                            ]
                        ]
                    ]
                ],
                [
                    "type" => "resource_subject_format_subject",
                    "id" => "new_7",
                    "attributes" => [
                        "resource_id" => "{$falshResource->id}",
                        "resource_slug" => "flash",
                        "is_active" => true
                    ],
                    "relationships" => [
                        "learningResourceAcceptCriteria" => [
                            "data" => [

                                "type" => "AcceptCriteria",
                                "id" => "48de6f75-994c-4b10-ae5c-173740c8c198"

                            ]
                        ]
                    ]
                ],
                [
                    "type" => "AcceptCriteria",
                    "id" => "bf05eb7e-45a2-4238-a316-6863767b6bc1",
                    "attributes" => [
                        "due_date" => 4,
                        "description" => "Testt",
                        "video_type" => "1",

                    ]
                ],
                [
                    "type" => "AcceptCriteria",
                    "id" => "bf05eb7e-45a2-4238-a316-6863767b6bc3",
                    "attributes" => [
                        "due_date" => 4,
                        "description" => "Testt",
                        "audio_type" => "1",
                        "number_of_question" => 5,
                        "difficulty_level" => 38,
                        "learning_outcome" => 2,


                    ]
                ],
                [
                    "type" => "AcceptCriteria",
                    "id" => "48de6f75-994c-4b10-ae5c-173740c8c198",
                    "attributes" => [
                        "due_date" => 4,
                        "description" => "flash",
                        "flash" => 'file',
                    ]
                ],
                [
                    "type" => "AcceptCriteria",
                    "id" => "48de6f75-994c-4b10-ae5c-173759c8c198",
                    "attributes" => [
                        "due_date" => 1,
                        "description" => "sda",
                        "number_of_question" => 5,
                        "difficulty_level" => "1",
                        "learning_outcome" => "2",
                        "drag_drop_type" => "5",
                    ]
                ],
                [   // PDF Acceptance criteria
                    "type" => "AcceptCriteria",
                    "id" => "69f1034f-dfaa-44d4-ab56-d6e9ca144bee",
                    "attributes" => [
                        "due_date" => 4,
                        "description" => "PDF test",
                        "pdf_type" => "URL"
                    ]
                ],
            ]
        ];


        $response = $this->putJson("/api/v1/en/sme/subjects/{$record->id}/structural", $requestData)->assertOk();
        $this->assertDatabaseHas('subjects', [
            'total_points' => LearningResourcesPointsEnums::AUDIO +
                              LearningResourcesPointsEnums::VIDEO +
                              LearningResourcesPointsEnums::FLASH +
                              LearningResourcesPointsEnums::PDF
        ]);
    }
}
