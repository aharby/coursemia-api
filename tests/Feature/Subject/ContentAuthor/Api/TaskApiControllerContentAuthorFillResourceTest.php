<?php

namespace Tests\Feature\Subject\ContentAuthor\Api;

use App\OurEdu\ResourceSubjectFormats\Models\Flash\FlashData;
use App\OurEdu\ResourceSubjectFormats\Models\HotSpot\HotSpotData;
use App\OurEdu\ResourceSubjectFormats\Models\Picture\PictureData;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\OurEdu\Options\Option;
use Illuminate\Http\UploadedFile;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\GarbageMedia\GarbageMedia;
use App\OurEdu\LearningResources\Resource;
use Illuminate\Foundation\Testing\WithFaker;
use App\OurEdu\Subjects\Models\SubModels\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\OurEdu\Subjects\Models\SubModels\ContentAuthorTask;
use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use App\OurEdu\ResourceSubjectFormats\Models\Flash\FlashDataMedia;
use App\OurEdu\ResourceSubjectFormats\Models\Complete\CompleteData;
use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropData;
use App\OurEdu\ResourceSubjectFormats\Models\Matching\MatchingData;
use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropOption;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseData;
use App\OurEdu\ResourceSubjectFormats\Models\Picture\PictureDataMedia;
use App\OurEdu\ResourceSubjectFormats\Models\Complete\CompleteQuestion;
use App\OurEdu\ResourceSubjectFormats\Models\Matching\MatchingQuestion;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseQuestion;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use App\OurEdu\ResourceSubjectFormats\Models\MultiMatching\MultiMatchingData;
use App\OurEdu\ResourceSubjectFormats\Models\MultiMatching\MultiMatchingQuestion;

class FillResourceTest extends TestCase
{
    use WithFaker;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_fill_resource_true_false()
    {
        $this->disableExceptionHandling();

        dump('test_fill_resource_true_false');
        $sme = $this->authSME();

        $subject = factory(Subject::class)->create();
        $subject->update(['sme_id' => $sme->id]);

        $contentAuthor1 = $this->authContentAuthor();
        $contentAuthor2 = $this->authContentAuthor();
        $subject->contentAuthors()->attach([
            $contentAuthor1->id,
            $contentAuthor2->id,
        ]);

        $resourceData = [
            'title' => LearningResourcesEnums::TRUE_FALSE,
            'slug' => LearningResourcesEnums::TRUE_FALSE,
        ];

        $resource = factory(Resource::class)->create($resourceData);

        $section1 = factory(SubjectFormatSubject::class)->create(['subject_id' => $subject->id]);

        $resourceSection1 = factory(ResourceSubjectFormatSubject::class)->create([
            'subject_format_subject_id' => $section1->id,
            'resource_id' => $resource->id,
            'resource_slug' => $resource->slug,
        ]);

        $taskResourceSection1 = factory(Task::class)->create([
            'subject_id' => $subject->id,
            'resource_subject_format_subject_id' => $resourceSection1->id,
            'subject_format_subject_id' => $section1->id,
            'is_assigned' => 0,
        ]);

        ContentAuthorTask::create([
            'task_id' => $taskResourceSection1->id,
            'content_author_id' => $contentAuthor1->contentAuthor->id,
        ]);


        $requestData = [
            'data' =>
                [
                    'type' => 'resource_subject_format_subject',
                    'id' => "{$taskResourceSection1->id}",
                    'attributes' =>
                        [
                            'resource_id' => '1',
                            'resource_slug' => 'true_false',
                            'is_active' => true,
                            'is_editable' => true,
                        ],
                    'relationships' =>
                        [
                            'learningResourceAcceptCriteria' =>
                                [
                                    'data' =>
                                        [
                                            'type' => 'learning_resource_accept_criteria_field',
                                            'id' => '1dd450e0-682b-4c2d-82d9-64e380e2f7d5',
                                        ]
                                ],
                            'resourceSubjectFormatSubjectData' =>
                                [
                                    'data' =>
                                        [
                                            'type' => 'resource_subject_format_subject_data',
                                            'id' => 'new',
                                        ]
                                ]
                        ]
                ],
            'included' =>
                [
                    [
                        'type' => 'learning_resource_accept_criteria_field',
                        'id' => '1dd450e0-682b-4c2d-82d9-64e380e2f7d5',
                        'attributes' =>
                            [
                                'due_date' => 4,
                                'description' => 'Testt',
                                'true_false_type' => '1',
                            ]
                    ],
                    [
                        'type' => 'resource_subject_format_subject_data',
                        'id' => 'new',
                        'attributes' =>
                            [
                                'description' => 'description',
                                'questions' =>
                                    [
                                        [
                                            'id' => 'new',
                                            'text' => 'q1',
                                            'image' => 'ddd',
                                            'is_true' => false,
                                            'options' =>
                                                [
                                                    [
                                                        'id' => 'new1',
                                                        'option' => 'option1',
                                                        'is_correct' => true,
                                                    ],
                                                    [
                                                        'id' => 'new2',
                                                        'option' => 'option2',
                                                        'is_correct' => false,
                                                    ]
                                                ]
                                        ]
                                    ]
                            ]
                    ]
                ]
        ];

        $this->apiSignIn($contentAuthor1);

        $response = $this->putJson(
            "/api/v1/en/content-author/subjects/resources/{$resourceSection1->id}/fill",
            $requestData
        )
            ->assertOk();
        $response->assertJsonStructure(
            [
                'data' =>
                    [
                        'type',
                        'id',
                        'attributes' =>
                            [
                                'resource_id',
                                'resource_slug',
                                'is_active',
                                'is_editable',
                            ],
                        'relationships' =>
                            [
                                'learningResourceAcceptCriteria' =>
                                    [
                                        'data' =>
                                            [
                                                'type',
                                                'id',
                                            ]
                                    ],
                                'resourceSubjectFormatSubjectData' =>
                                    [
                                        'data' =>
                                            [
                                                'type',
                                                'id',
                                            ]
                                    ]
                            ]
                    ],
                'included' =>
                    [

                        [
                            'type',
                            'id',
                            'attributes' =>
                                [
                                    'due_date',
                                    'description',
                                ]
                        ],
                        [
                            'type',
                            'id',
                            'attributes' =>
                                [
                                    'description',
                                    'questions' =>
                                        [
                                            [
                                                'id',
                                                'text',
                                                'is_true',
                                                'options' =>
                                                    [
                                                        [
                                                            'id',
                                                            'option',
                                                            'is_correct',
                                                        ],
                                                        [
                                                            'id',
                                                            'option',
                                                            'is_correct',
                                                        ]
                                                    ]
                                            ]
                                        ]
                                ]
                        ]
                    ]
            ]
        );

        $trueFalseData = [
            'description' => 'description',
            'resource_subject_format_subject_id' => $resourceSection1->id,
            //            'true_false_type' => $contentAuthor1->contentAuthor->id,

        ];
        $this->assertDatabaseHas('res_true_false_data', $trueFalseData);

        $trueFalseData = TrueFalseData::where($trueFalseData)->first();

        $resTrueFalseQuestionsData = [
            'text' => 'q1',
            'res_true_false_data_id' => $trueFalseData->id,
        ];

        $this->assertDatabaseHas('res_true_false_questions', $resTrueFalseQuestionsData);

        $trueFalseQuestion = TrueFalseQuestion::where($resTrueFalseQuestionsData)->first();


        $optionData1 = [
            'option' => 'option1',
            'is_correct_answer' => true,
            'res_true_false_question_id' => $trueFalseQuestion->id,
        ];
        $this->assertDatabaseHas('res_true_false_options', $optionData1);

        $optionData2 = [
            'option' => 'option2',
            'is_correct_answer' => false,
            'res_true_false_question_id' => $trueFalseQuestion->id,
        ];
        $this->assertDatabaseHas('res_true_false_options', $optionData2);
    }

    public function test_fill_resource_matching()
    {
        dump('test_matching');
        $sme = $this->authSME();

        $subject = factory(Subject::class)->create();

        $subject->update(['sme_id' => $sme->id]);

        $contentAuthor1 = $this->authContentAuthor();
        $contentAuthor2 = $this->authContentAuthor();
        $subject->contentAuthors()->attach([
            $contentAuthor1->id,
            $contentAuthor2->id,
        ]);


        $resourceData = [
            'title' => LearningResourcesEnums::MATCHING,
            'slug' => LearningResourcesEnums::MATCHING,
        ];
        $resource = factory(Resource::class)->create($resourceData);

        $section1 = factory(SubjectFormatSubject::class)->create();
        $resourceSection1 = factory(ResourceSubjectFormatSubject::class)->create([
            'subject_format_subject_id' => $section1->id,
            'resource_id' => $resource->id,
            'resource_slug' => $resource->slug,
        ]);

        $taskResourceSection1 = factory(Task::class)->create([
            'subject_id' => $subject->id,
            'resource_subject_format_subject_id' => $resourceSection1->id,
            'subject_format_subject_id' => $section1->id,
            'is_assigned' => 0,
        ]);

        ContentAuthorTask::create([
            'task_id' => $taskResourceSection1->id,
            'content_author_id' => $contentAuthor1->contentAuthor->id,
        ]);


        $requestData = [
            'data' =>
                [
                    'type' => 'resource_subject_format_subject',
                    'id' => "{$taskResourceSection1->id}",
                    'attributes' =>
                        [
                            'resource_id' => '9',
                            'resource_slug' => 'matching',
                            'is_active' => true,
                            'is_editable' => true,
                        ],
                    'relationships' =>
                        [
                            'learningResourceAcceptCriteria' =>
                                [
                                    'data' =>
                                        [
                                            'type' => 'learning_resource_accept_criteria_field',
                                            'id' => '1dd450e0-682b-4c2d-82d9-64e380e2f7d5',
                                        ]
                                ],
                            'resourceSubjectFormatSubjectData' =>
                                [
                                    'data' =>
                                        [
                                            'type' => 'resource_subject_format_subject_data',
                                            'id' => 'new',
                                        ]
                                ]
                        ]
                ],
            'included' =>
                [
                    [
                        'type' => 'learning_resource_accept_criteria_field',
                        'id' => '1dd450e0-682b-4c2d-82d9-64e380e2f7d5',
                        'attributes' =>
                            [
                                'due_date' => 4,
                                'description' => 'Testt',
                                'matching_type' => '9',
                            ]
                    ],
                    [
                        'type' => 'resource_subject_format_subject_data',
                        'id' => 'new',
                        'attributes' =>
                            [
                                'description' => 'description',
                                'questions' =>
                                    [
                                        [
                                            'id' => 'new',
                                            'text' => 'q1'
                                        ]
                                    ] ,
                                'options' =>
                                    [
                                        [
                                            'id' => 'new1',
                                            'option' => 'option1',
                                            'question_id' => 'new',
                                        ],
                                        [
                                            'id' => 'new2',
                                            'option' => 'option2',
                                            'question_id' => null
                                        ]
                                    ]
                            ]
                    ]
                ]
        ];
        $this->apiSignIn($contentAuthor1);

        $response = $this->putJson(
            "/api/v1/en/content-author/subjects/resources/{$resourceSection1->id}/fill",
            $requestData
        )
            ->assertOk();
        $response->assertJsonStructure(
            [
                'data' =>
                    [
                        'type',
                        'id',
                        'attributes' =>
                            [
                                'resource_id',
                                'resource_slug',
                                'is_active',
                                'is_editable',
                            ],
                        'relationships' =>
                            [
                                'learningResourceAcceptCriteria' =>
                                    [
                                        'data' =>
                                            [
                                                'type',
                                                'id',
                                            ]
                                    ],
                                'resourceSubjectFormatSubjectData' =>
                                    [
                                        'data' =>
                                            [
                                                'type',
                                                'id',
                                            ]
                                    ]
                            ]
                    ],
                'included' =>
                    [

                        [
                            'type',
                            'id',
                            'attributes' =>
                                [
                                    'due_date',
                                    'description',
                                ]
                        ],
                        [
                            'type',
                            'id',
                            'attributes' =>
                                [
                                    'description',
                                    'questions' =>
                                        [
                                            [
                                                'id',
                                                'text',
                                            ]
                                        ] ,
                                    'options' =>
                                        [
                                            [
                                                'id',
                                                'option',
                                                'question_id'
                                            ]
                                        ] ,

                                ]
                        ]
                    ]
            ]
        );

        $matchingData = [
            'description' => 'description',
            'resource_subject_format_subject_id' => $resourceSection1->id,
            //            'true_false_type' => $contentAuthor1->contentAuthor->id,

        ];
        $this->assertDatabaseHas('res_matching_data', $matchingData);

        $matchingData = MatchingData::where($matchingData)->first();

        $matchingQuestionsData = [
            'text' => 'q1',
            'res_matching_data_id' => $matchingData->id,
        ];

        $this->assertDatabaseHas('res_matching_questions', $matchingQuestionsData);

        $matchingQuestion = MatchingQuestion::where($matchingQuestionsData)->first();


        $optionData1 = [
            'option' => 'option1',
            'res_matching_question_id' => $matchingQuestion->id,
        ];
        $this->assertDatabaseHas('res_matching_options', $optionData1);

        $optionData2 = [
            'option' => 'option2',
            'res_matching_question_id' => null,
        ];
        $this->assertDatabaseHas('res_matching_options', $optionData2);
    }

    public function test_fill_resource_multi_matching()
    {
        dump('test_multi_matching');
        $sme = $this->authSME();

        $subject = factory(Subject::class)->create();
//        $subjectFormatSubject = factory(SubjectFormatSubject::class)->create();

        $subject->update(['sme_id' => $sme->id]);

        $contentAuthor1 = $this->authContentAuthor();
        $contentAuthor2 = $this->authContentAuthor();
        $subject->contentAuthors()->attach([
            $contentAuthor1->id,
            $contentAuthor2->id,
        ]);


        $resourceData = [
            'title' => LearningResourcesEnums::MULTIPLE_MATCHING,
            'slug' => LearningResourcesEnums::MULTIPLE_MATCHING,
        ];
        $resource = factory(Resource::class)->create($resourceData);

        $section1 = factory(SubjectFormatSubject::class)->create();
        $resourceSection1 = factory(ResourceSubjectFormatSubject::class)->create([
            'subject_format_subject_id' => $section1->id,
            'resource_id' => $resource->id,
            'resource_slug' => $resource->slug,
        ]);

        $taskResourceSection1 = factory(Task::class)->create([
            'subject_id' => $subject->id,
            'resource_subject_format_subject_id' => $resourceSection1->id,
            'subject_format_subject_id' => $section1->id,
            'is_assigned' => 0,
        ]);

        ContentAuthorTask::create([
            'task_id' => $taskResourceSection1->id,
            'content_author_id' => $contentAuthor1->contentAuthor->id,
        ]);


        $requestData = [
            'data' =>
                [
                    'type' => 'resource_subject_format_subject',
                    'id' => "{$taskResourceSection1->id}",
                    'attributes' =>
                        [
                            'resource_id' => '10',
                            'resource_slug' => 'multiple_matching',
                            'is_active' => true,
                            'is_editable' => true,
                        ],
                    'relationships' =>
                        [
                            'learningResourceAcceptCriteria' =>
                                [
                                    'data' =>
                                        [
                                            'type' => 'learning_resource_accept_criteria_field',
                                            'id' => '1dd450e0-682b-4c2d-82d9-64e380e2f7d5',
                                        ]
                                ],
                            'resourceSubjectFormatSubjectData' =>
                                [
                                    'data' =>
                                        [
                                            'type' => 'resource_subject_format_subject_data',
                                            'id' => 'new',
                                        ]
                                ]
                        ]
                ],
            'included' =>
                [
                    [
                        'type' => 'learning_resource_accept_criteria_field',
                        'id' => '1dd450e0-682b-4c2d-82d9-64e380e2f7d5',
                        'attributes' =>
                            [
                                'due_date' => 4,
                                'description' => 'Testt',
                                'multiple_matching_type' => '10',
                            ]
                    ],
                    [
                        'type' => 'resource_subject_format_subject_data',
                        'id' => 'new',
                        'attributes' =>
                            [
                                'description' => 'description',
                                'questions' =>
                                    [
                                        [
                                            'id' => 'new',
                                            'text' => 'q1',
                                        ]
                                    ],
                                'options' =>
                                    [
                                        [
                                            'id' => 'new1',
                                            'option' => 'option1',
                                            'questions' => ['new'],
                                        ],
                                        [
                                            'id' => 'new2',
                                            'option' => 'option2',
                                            'questions' => ['new'],
                                        ]
                                    ]
                            ]
                    ]
                ]
        ];
        $this->apiSignIn($contentAuthor1);

        $response = $this->putJson(
            "/api/v1/en/content-author/subjects/resources/{$resourceSection1->id}/fill",
            $requestData
        )
            ->assertOk();
        $response->assertJsonStructure(
            [
                'data' =>
                    [
                        'type',
                        'id',
                        'attributes' =>
                            [
                                'resource_id',
                                'resource_slug',
                                'is_active',
                                'is_editable',
                            ],
                        'relationships' =>
                            [
                                'learningResourceAcceptCriteria' =>
                                    [
                                        'data' =>
                                            [
                                                'type',
                                                'id',
                                            ]
                                    ],
                                'resourceSubjectFormatSubjectData' =>
                                    [
                                        'data' =>
                                            [
                                                'type',
                                                'id',
                                            ]
                                    ]
                            ]
                    ],
                'included' =>
                    [

                        [
                            'type',
                            'id',
                            'attributes' =>
                                [
                                    'due_date',
                                    'description',
                                ]
                        ],
                        [
                            'type',
                            'id',
                            'attributes' =>
                                [
                                    'description',
                                    'questions' =>
                                        [
                                            [
                                                'id',
                                                'text',
                                            ]
                                        ] ,
                                    'options' =>
                                        [
                                            [
                                                'id',
                                                'option',
                                                'questions',
                                            ],
                                            [
                                                'id',
                                                'option',
                                                'questions',
                                            ]
                                        ]
                                ]
                        ]
                    ]
            ]
        );

        $multiMatchingData = [
            'description' => 'description',
            'resource_subject_format_subject_id' => $resourceSection1->id,
            //            'true_false_type' => $contentAuthor1->contentAuthor->id,

        ];
        $this->assertDatabaseHas('res_multi_matching_data', $multiMatchingData);

        $multiMatchingData = MultiMatchingData::where($multiMatchingData)->first();

        $multiMatchingQuestionsData = [
            'text' => 'q1',
            'res_multi_matching_data_id' => $multiMatchingData->id,
        ];

        $this->assertDatabaseHas('res_multi_matching_questions', $multiMatchingQuestionsData);

        $multiMatchingQuestion = MultiMatchingQuestion::where($multiMatchingQuestionsData)->first();


        $optionData1 = [
            'option' => 'option1',
        ];
        $this->assertDatabaseHas('res_multi_matching_options', $optionData1);

        $optionData2 = [
            'option' => 'option2',
        ];
        $this->assertDatabaseHas('res_multi_matching_options', $optionData2);
    }

    public function test_fill_resource_video()
    {
        dump('test_fill_resource_video');
        $sme = $this->authSME();

        $subject = factory(Subject::class)->create();

        $subject->update(['sme_id' => $sme->id]);

        $contentAuthor1 = $this->authContentAuthor();
        $contentAuthor2 = $this->authContentAuthor();
        $subject->contentAuthors()->attach([
            $contentAuthor1->id,
            $contentAuthor2->id,
        ]);


        $resourceData = [
            'title' => LearningResourcesEnums::Video,
            'slug' => LearningResourcesEnums::Video,
        ];
        $resource = factory(Resource::class)->create($resourceData);

        $section1 = factory(SubjectFormatSubject::class)->create();
        $resourceSection1 = factory(ResourceSubjectFormatSubject::class)->create([
            'subject_format_subject_id' => $section1->id,
            'resource_id' => $resource->id,
            'resource_slug' => $resource->slug,
        ]);

        $taskResourceSection1 = factory(Task::class)->create([
            'subject_id' => $subject->id,
            'resource_subject_format_subject_id' => $resourceSection1->id,
            'subject_format_subject_id' => $section1->id,
            'is_assigned' => 0,
        ]);

        ContentAuthorTask::create([
            'task_id' => $taskResourceSection1->id,
            'content_author_id' => $contentAuthor1->contentAuthor->id,
        ]);


        $requestData = [
            'data' =>
                [
                    'type' => 'resource_subject_format_subject',
                    'id' => "{$taskResourceSection1->id}",
                    'attributes' =>
                        [
                            'resource_id' => '1',
                            'resource_slug' => 'video',
                            'is_active' => true,
                            'is_editable' => true,
                        ],
                    'relationships' =>
                        [
                            'learningResourceAcceptCriteria' =>
                                [
                                    'data' =>
                                        [
                                            'type' => 'learning_resource_accept_criteria_field',
                                            'id' => '1dd450e0-682b-4c2d-82d9-64e380e2f7d5',
                                        ]
                                ],
                            'resourceSubjectFormatSubjectData' =>
                                [
                                    'data' =>
                                        [
                                            'type' => 'resource_subject_format_subject_data',
                                            'id' => 'new',
                                        ]
                                ]
                        ]
                ],
            'included' =>
                [
                    [
                        'type' => 'learning_resource_accept_criteria_field',
                        'id' => '1dd450e0-682b-4c2d-82d9-64e380e2f7d5',
                        'attributes' =>
                            [
                                'due_date' => 4,
                                'description' => 'Testt',
                                'video_type' => '1',
                            ]
                    ],
                    [
                        'type' => 'resource_subject_format_subject_data',
                        'id' => 'new',
                        'attributes' =>
                            [
                                'description' => 'description',
                                'video' => 'http://www.ouredue.testenv.com/video'
                            ]
                    ]
                ]
        ];
        $this->apiSignIn($contentAuthor1);

        $response = $this->putJson(
            "/api/v1/en/content-author/subjects/resources/{$resourceSection1->id}/fill",
            $requestData
        )
            ->assertOk();

        $response->assertJsonStructure(
            [
                'data' =>
                    [
                        'type',
                        'id',
                        'attributes' =>
                            [
                                'resource_id',
                                'resource_slug',
                                'is_active',
                                'is_editable',
                            ],
                        'relationships' =>
                            [
                                'learningResourceAcceptCriteria' =>
                                    [
                                        'data' =>
                                            [
                                                'type',
                                                'id',
                                            ]
                                    ],
                                'resourceSubjectFormatSubjectData' =>
                                    [
                                        'data' =>
                                            [
                                                'type',
                                                'id',
                                            ]
                                    ]
                            ]
                    ],
                'included' =>
                    [

                        [
                            'type',
                            'id',
                            'attributes' =>
                                [
                                    'due_date',
                                    'description',
                                ]
                        ],
                        [
                            'type',
                            'id',
                            'attributes' =>
                                [
                                    'description',
                                    'video_type'
                                ]
                        ]
                    ]
            ]
        );
    }


    public function test_fill_resource_drag_drop()
    {
        dump('fill_resource_drag_drop');
        $sme = $this->authSME();

        $subject = factory(Subject::class)->create();
//        $subjectFormatSubject = factory(SubjectFormatSubject::class)->create();

        $subject->update(['sme_id' => $sme->id]);

        $contentAuthor1 = $this->authContentAuthor();
        $contentAuthor2 = $this->authContentAuthor();
        $subject->contentAuthors()->attach([
            $contentAuthor1->id,
            $contentAuthor2->id,
        ]);


        $resourceData = [
            'title' => LearningResourcesEnums::DRAG_DROP,
            'slug' => LearningResourcesEnums::DRAG_DROP,
        ];
        $resource = factory(Resource::class)->create($resourceData);

        $section1 = factory(SubjectFormatSubject::class)->create();
        $resourceSection1 = factory(ResourceSubjectFormatSubject::class)->create([
            'subject_format_subject_id' => $section1->id,
            'resource_id' => $resource->id,
            'resource_slug' => $resource->slug,
        ]);

        $taskResourceSection1 = factory(Task::class)->create([
            'subject_id' => $subject->id,
            'resource_subject_format_subject_id' => $resourceSection1->id,
            'subject_format_subject_id' => $section1->id,
            'is_assigned' => 0,
        ]);

        ContentAuthorTask::create([
            'task_id' => $taskResourceSection1->id,
            'content_author_id' => $contentAuthor1->contentAuthor->id,
        ]);


        $requestData = [
            'data' =>
                [
                    'type' => 'resource_subject_format_subject',
                    'id' => "{$taskResourceSection1->id}",
                    'attributes' =>
                        [
                            'resource_id' => "{$resource->id}",
                            'resource_slug' => "{$resource->slug}",
                            'is_active' => true,
                            'is_editable' => true,
                        ],
                    'relationships' =>
                        [
                            'learningResourceAcceptCriteria' =>
                                [
                                    'data' =>
                                        [
                                            'type' => 'learning_resource_accept_criteria_field',
                                            'id' => '1dd450e0-682b-4c2d-82d9-64e380e2f7d5',
                                        ]
                                ],
                            'resourceSubjectFormatSubjectData' =>
                                [
                                    'data' =>
                                        [
                                            'type' => 'resource_subject_format_subject_data',
                                            'id' => 'new',
                                        ]
                                ]
                        ]
                ],
            'included' =>
                [
                    [
                        'type' => 'learning_resource_accept_criteria_field',
                        'id' => '1dd450e0-682b-4c2d-82d9-64e380e2f7d5',
                        'attributes' =>
                            [
                                'due_date' => 4,
                                'description' => 'Testt',
                                'true_false_type' => '1',
                            ]
                    ],
                    [
                        'type' => 'resource_subject_format_subject_data',
                        'id' => 'new',
                        'attributes' =>
                            [
                                'description' => 'description',
                                'questions' =>
                                    [
                                        [
                                            'id' => 'new',
                                            'question' => 'q1',
                                            'answers' => 'new_1',

                                        ]
                                    ],
                                'options' =>
                                    [
                                        [
                                            'id' => 'new_1',
                                            'option' => 'option1',

                                        ]
                                    ]
                            ]
                    ]
                ]
        ];

        $this->apiSignIn($contentAuthor1);

        $response = $this->putJson(
            "/api/v1/en/content-author/subjects/resources/{$resourceSection1->id}/fill",
            $requestData
        )
            ->assertOk();
        $response->assertJsonStructure(
            [
                'data' =>
                    [
                        'type',
                        'id',
                        'attributes' =>
                            [
                                'resource_id',
                                'resource_slug',
                                'is_active',
                                'is_editable',
                            ],
                        'relationships' =>
                            [
                                'learningResourceAcceptCriteria' =>
                                    [
                                        'data' =>
                                            [
                                                'type',
                                                'id',
                                            ]
                                    ],
                                'resourceSubjectFormatSubjectData' =>
                                    [
                                        'data' =>
                                            [
                                                'type',
                                                'id',
                                            ]
                                    ]
                            ]
                    ],
                'included' =>
                    [

                        [
                            'type',
                            'id',
                            'attributes' =>
                                [
                                    'due_date',
                                    'description',
                                ]
                        ],
                        [
                            'type',
                            'id',
                            'attributes' =>
                                [
                                    'description',
                                    'questions' =>
                                        [
                                            [
                                                'id',
                                                'question',
                                                'answers',
                                            ]
                                        ],
                                    'options' =>
                                        [
                                            [
                                                'id',
                                                'option',

                                            ]
                                        ]
                                ]
                        ]
                    ]
            ]
        );

        $dragDropData = [
            'description' => 'description',
            'resource_subject_format_subject_id' => $resourceSection1->id,
            //            'true_false_type' => $contentAuthor1->contentAuthor->id,

        ];
        $this->assertDatabaseHas('res_drag_drop_data', $dragDropData);

        $dragDropData = DragDropData::where($dragDropData)->first();


        $optionData1 = [
            'option' => 'option1',
            'res_drag_drop_data_id' => $dragDropData->id,
        ];
        $dragDropOption = DragDropOption::where($optionData1)->first();

        $this->assertDatabaseHas('res_drag_drop_options', $optionData1);


        $resDragDropQuestionsData = [
            'question' => 'q1',
            'res_drag_drop_data_id' => $dragDropData->id,
            'correct_option_id' => $dragDropOption->id,
        ];

        $this->assertDatabaseHas('res_drag_drop_questions', $resDragDropQuestionsData);
    }

    public function test_fill_resource_page()
    {
        dump('test_fill_resource_page');
        $sme = $this->authSME();

        $subject = factory(Subject::class)->create();
//        $subjectFormatSubject = factory(SubjectFormatSubject::class)->create();

        $subject->update(['sme_id' => $sme->id]);

        $contentAuthor1 = $this->authContentAuthor();
        $contentAuthor2 = $this->authContentAuthor();
        $subject->contentAuthors()->attach([
            $contentAuthor1->id,
            $contentAuthor2->id,
        ]);


        $resourceData = [
            'title' => LearningResourcesEnums::PAGE,
            'slug' => LearningResourcesEnums::PAGE,
        ];
        $resource = factory(Resource::class)->create($resourceData);

        $section1 = factory(SubjectFormatSubject::class)->create();
        $resourceSection1 = factory(ResourceSubjectFormatSubject::class)->create([
            'subject_format_subject_id' => $section1->id,
            'resource_id' => $resource->id,
            'resource_slug' => $resource->slug,
        ]);

        $taskResourceSection1 = factory(Task::class)->create([
            'subject_id' => $subject->id,
            'resource_subject_format_subject_id' => $resourceSection1->id,
            'subject_format_subject_id' => $section1->id,
            'is_assigned' => 0,
        ]);

        ContentAuthorTask::create([
            'task_id' => $taskResourceSection1->id,
            'content_author_id' => $contentAuthor1->contentAuthor->id,
        ]);

        $requestData = [
            'data' =>
                [
                    'type' => 'resource_subject_format_subject',
                    'id' => "{$taskResourceSection1->id}",
                    'attributes' =>
                        [
                            'resource_id' => "{$resource->id}",
                            'resource_slug' => "{$resource->slug}",
                            'is_active' => true,
                            'is_editable' => true,
                        ],
                    'relationships' =>
                        [
                            'learningResourceAcceptCriteria' =>
                                [
                                    'data' =>
                                        [
                                            'type' => 'learning_resource_accept_criteria_field',
                                            'id' => '1dd450e0-682b-4c2d-82d9-64e380e2f7d5',
                                        ]
                                ],
                            'resourceSubjectFormatSubjectData' =>
                                [
                                    'data' =>
                                        [
                                            'type' => 'resource_subject_format_subject_data',
                                            'id' => 'new',
                                        ]
                                ]
                        ]
                ],
            'included' =>
                [
                    [
                        'type' => 'learning_resource_accept_criteria_field',
                        'id' => '1dd450e0-682b-4c2d-82d9-64e380e2f7d5',
                        'attributes' =>
                            [
                                'due_date' => 4,
                                'description' => 'Testt',
                                'true_false_type' => '1',
                            ]
                    ],
                    [
                        'type' => 'resource_subject_format_subject_data',
                        'id' => 'new',
                        'attributes' =>
                            [
                                'page' => 'page content',

                            ]
                    ]
                ]
        ];
        $this->apiSignIn($contentAuthor1);

        $response = $this->putJson(
            "/api/v1/en/content-author/subjects/resources/{$resourceSection1->id}/fill",
            $requestData
        )
            ->assertOk();

        $pageData = [
            'resource_subject_format_subject_id' => $resourceSection1->id,
            'page' => 'page content',

        ];
        $this->assertDatabaseHas('res_page_data', $pageData);

        $response->assertJsonStructure(
            [
                'data' =>
                    [
                        'type',
                        'id',
                        'attributes' =>
                            [
                                'resource_id',
                                'resource_slug',
                                'is_active',
                                'is_editable',
                            ],
                        'relationships' =>
                            [
                                'learningResourceAcceptCriteria' =>
                                    [
                                        'data' =>
                                            [
                                                'type',
                                                'id',
                                            ]
                                    ],
                                'resourceSubjectFormatSubjectData' =>
                                    [
                                        'data' =>
                                            [
                                                'type',
                                                'id',
                                            ]
                                    ]
                            ]
                    ],
                'included' =>
                    [

                        [
                            'type',
                            'id',
                            'attributes' =>
                                [
                                    'due_date',
                                    'description',
                                ]
                        ],
                        [
                            'type',
                            'id',
                            'attributes' =>
                                [
                                    'page',

                                ]
                        ]
                    ]
            ]
        );
    }

    public function test_fill_resource_audio()
    {
        dump('test_fill_resource_audio');
        $sme = $this->authSME();

        $subject = factory(Subject::class)->create();

        $subject->update(['sme_id' => $sme->id]);

        $contentAuthor1 = $this->authContentAuthor();
        $contentAuthor2 = $this->authContentAuthor();
        $subject->contentAuthors()->attach([
            $contentAuthor1->id,
            $contentAuthor2->id,
        ]);


        $resourceData = [
            'title' => LearningResourcesEnums::Audio,
            'slug' => LearningResourcesEnums::Audio,
        ];
        $resource = factory(Resource::class)->create($resourceData);

        $section1 = factory(SubjectFormatSubject::class)->create();
        $resourceSection1 = factory(ResourceSubjectFormatSubject::class)->create([
            'subject_format_subject_id' => $section1->id,
            'resource_id' => $resource->id,
            'resource_slug' => $resource->slug,
        ]);

        $taskResourceSection1 = factory(Task::class)->create([
            'subject_id' => $subject->id,
            'resource_subject_format_subject_id' => $resourceSection1->id,
            'subject_format_subject_id' => $section1->id,
            'is_assigned' => 0,
        ]);

        ContentAuthorTask::create([
            'task_id' => $taskResourceSection1->id,
            'content_author_id' => $contentAuthor1->contentAuthor->id,
        ]);


        $requestData = [
            'data' =>
                [
                    'type' => 'resource_subject_format_subject',
                    'id' => "{$taskResourceSection1->id}",
                    'attributes' =>
                        [
                            'resource_id' => '3',
                            'resource_slug' => 'audio',
                            'is_active' => true,
                            'is_editable' => true,
                        ],
                    'relationships' =>
                        [
                            'learningResourceAcceptCriteria' =>
                                [
                                    'data' =>
                                        [
                                            'type' => 'learning_resource_accept_criteria_field',
                                            'id' => '1dd450e0-682b-4c2d-82d9-64e380e2f7d5',
                                        ]
                                ],
                            'resourceSubjectFormatSubjectData' =>
                                [
                                    'data' =>
                                        [
                                            'type' => 'resource_subject_format_subject_data',
                                            'id' => 'new',
                                        ]
                                ]
                        ]
                ],
            'included' =>
                [
                    [
                        'type' => 'learning_resource_accept_criteria_field',
                        'id' => '1dd450e0-682b-4c2d-82d9-64e380e2f7d5',
                        'attributes' =>
                            [
                                'due_date' => 4,
                                'description' => 'Testt',
                                'audio_type' => '1',
                            ]
                    ],
                    [
                        'type' => 'resource_subject_format_subject_data',
                        'id' => 'new',
                        'attributes' =>
                            [
                                'description' => 'description',
                                'audio' => 'http://www.ouredue.testenv.com/video'
                            ]
                    ]
                ]
        ];
        $this->apiSignIn($contentAuthor1);

        $response = $this->putJson(
            "/api/v1/en/content-author/subjects/resources/{$resourceSection1->id}/fill",
            $requestData
        )
            ->assertOk();

        $response->assertJsonStructure(
            [
                'data' =>
                    [
                        'type',
                        'id',
                        'attributes' =>
                            [
                                'resource_id',
                                'resource_slug',
                                'is_active',
                                'is_editable',
                            ],
                        'relationships' =>
                            [
                                'learningResourceAcceptCriteria' =>
                                    [
                                        'data' =>
                                            [
                                                'type',
                                                'id',
                                            ]
                                    ],
                                'resourceSubjectFormatSubjectData' =>
                                    [
                                        'data' =>
                                            [
                                                'type',
                                                'id',
                                            ]
                                    ]
                            ]
                    ],
                'included' =>
                    [

                        [
                            'type',
                            'id',
                            'attributes' =>
                                [
                                    'due_date',
                                    'description',
                                ]
                        ],
                        [
                            'type',
                            'id',
                            'attributes' =>
                                [
                                    'description',
                                    'audio',
                                    'audio_type'
                                ]
                        ]
                    ]
            ]
        );
    }

    public function test_fill_resource_pdf()
    {
        dump('test_fill_resource_pdf');
        $sme = $this->authSME();

        $subject = factory(Subject::class)->create();

        $subject->update(['sme_id' => $sme->id]);

        $contentAuthor1 = $this->authContentAuthor();
        $contentAuthor2 = $this->authContentAuthor();
        $subject->contentAuthors()->attach([
            $contentAuthor1->id,
            $contentAuthor2->id,
        ]);


        $resourceData = [
            'title' => LearningResourcesEnums::PDF,
            'slug' => LearningResourcesEnums::PDF,
        ];
        $resource = factory(Resource::class)->create($resourceData);

        $section1 = factory(SubjectFormatSubject::class)->create();
        $resourceSection1 = factory(ResourceSubjectFormatSubject::class)->create([
            'subject_format_subject_id' => $section1->id,
            'resource_id' => $resource->id,
            'resource_slug' => $resource->slug,
        ]);

        $taskResourceSection1 = factory(Task::class)->create([
            'subject_id' => $subject->id,
            'resource_subject_format_subject_id' => $resourceSection1->id,
            'subject_format_subject_id' => $section1->id,
            'is_assigned' => 0,
        ]);

        ContentAuthorTask::create([
            'task_id' => $taskResourceSection1->id,
            'content_author_id' => $contentAuthor1->contentAuthor->id,
        ]);


        $requestData = [
            'data' =>
                [
                    'type' => 'resource_subject_format_subject',
                    'id' => "{$taskResourceSection1->id}",
                    'attributes' =>
                        [
                            'resource_id' => '6',
                            'resource_slug' => 'pdf',
                            'is_active' => true,
                            'is_editable' => true,
                        ],
                    'relationships' =>
                        [
                            'learningResourceAcceptCriteria' =>
                                [
                                    'data' =>
                                        [
                                            'type' => 'learning_resource_accept_criteria_field',
                                            'id' => '1dd450e0-682b-4c2d-82d9-64e380e2f7d5',
                                        ]
                                ],
                            'resourceSubjectFormatSubjectData' =>
                                [
                                    'data' =>
                                        [
                                            'type' => 'resource_subject_format_subject_data',
                                            'id' => 'new',
                                        ]
                                ]
                        ]
                ],
            'included' =>
                [
                    [
                        'type' => 'learning_resource_accept_criteria_field',
                        'id' => '1dd450e0-682b-4c2d-82d9-64e380e2f7d5',
                        'attributes' =>
                            [
                                'due_date' => 4,
                                'description' => 'Testt',
                                'audio_type' => '1',
                            ]
                    ],
                    [
                        'type' => 'resource_subject_format_subject_data',
                        'id' => 'new',
                        'attributes' =>
                            [
                                'description' => 'description',
                                'pdf' => 'http://www.ouredue.testenv.com/pdf.pdf'
                            ]
                    ]
                ]
        ];
        $this->apiSignIn($contentAuthor1);

        $response = $this->putJson(
            "/api/v1/en/content-author/subjects/resources/{$resourceSection1->id}/fill",
            $requestData
        )
            ->assertOk();

        $response->assertJsonStructure(
            [
                'data' =>
                    [
                        'type',
                        'id',
                        'attributes' =>
                            [
                                'resource_id',
                                'resource_slug',
                                'is_active',
                                'is_editable',
                            ],
                        'relationships' =>
                            [
                                'learningResourceAcceptCriteria' =>
                                    [
                                        'data' =>
                                            [
                                                'type',
                                                'id',
                                            ]
                                    ],
                                'resourceSubjectFormatSubjectData' =>
                                    [
                                        'data' =>
                                            [
                                                'type',
                                                'id',
                                            ]
                                    ]
                            ]
                    ],
                'included' =>
                    [

                        [
                            'type',
                            'id',
                            'attributes' =>
                                [
                                    'due_date',
                                    'description',
                                ]
                        ],
                        [
                            'type',
                            'id',
                            'attributes' =>
                                [
                                    'description',
                                    'pdf',
                                    'pdf_type'
                                ]
                        ]
                    ]
            ]
        );
    }

    public function test_fill_resource_multiple_choice()
    {
        dump('test_fill_resource_multiple_choice');

        $this->disableExceptionHandling();

        $sme = $this->authSME();

        $subject = factory(Subject::class)->create();

        $subject->update(['sme_id' => $sme->id]);

        $contentAuthor1 = $this->authContentAuthor();
        $contentAuthor2 = $this->authContentAuthor();
        $subject->contentAuthors()->attach([
            $contentAuthor1->id,
            $contentAuthor2->id,
        ]);


        $resourceData = [
            'title' => LearningResourcesEnums::MULTI_CHOICE,
            'slug' => LearningResourcesEnums::MULTI_CHOICE,
        ];
        $resource = factory(Resource::class)->create($resourceData);

        $section1 = factory(SubjectFormatSubject::class)->create();
        $resourceSection1 = factory(ResourceSubjectFormatSubject::class)->create([
            'subject_format_subject_id' => $section1->id,
            'resource_id' => $resource->id,
            'resource_slug' => $resource->slug,
        ]);

        $taskResourceSection1 = factory(Task::class)->create([
            'subject_id' => $subject->id,
            'resource_subject_format_subject_id' => $resourceSection1->id,
            'subject_format_subject_id' => $section1->id,
            'is_assigned' => 0,
        ]);

        ContentAuthorTask::create([
            'task_id' => $taskResourceSection1->id,
            'content_author_id' => $contentAuthor1->contentAuthor->id,
        ]);


        $requestData = [
            'data' =>
                [
                    'type' => 'resource_subject_format_subject',
                    'id' => "{$taskResourceSection1->id}",
                    'attributes' =>
                        [
                            'resource_id' => '1',
                            'resource_slug' => 'multiple_choice',
                            'is_active' => true,
                            'is_editable' => true,
                        ],
                    'relationships' =>
                        [
                            'learningResourceAcceptCriteria' =>
                                [
                                    'data' =>
                                        [
                                            'type' => 'learning_resource_accept_criteria_field',
                                            'id' => '1dd450e0-682b-4c2d-82d9-64e380e2f7d5',
                                        ]
                                ],
                            'resourceSubjectFormatSubjectData' =>
                                [
                                    'data' =>
                                        [
                                            'type' => 'resource_subject_format_subject_data',
                                            'id' => 'new',
                                        ]
                                ]
                        ]
                ],
            'included' =>
                [
                    [
                        'type' => 'learning_resource_accept_criteria_field',
                        'id' => '1dd450e0-682b-4c2d-82d9-64e380e2f7d5',
                        'attributes' =>
                            [
                                'due_date' => 4,
                                'description' => 'Testt',
                                'multiple_choice_type' => '1',
                            ]
                    ],
                    [
                        'type' => 'resource_subject_format_subject_data',
                        'id' => 'new',
                        'attributes' =>
                            [
                                'description' => 'description',
                                'questions' =>
                                    [
                                        [
                                            'id' => 'new',
                                            'question' => 'q1',
                                            'options' =>
                                                [
                                                    [
                                                        'id' => 'new1',
                                                        'option' => 'option1',
                                                        'is_correct_answer' => true,
                                                    ],
                                                    [
                                                        'id' => 'new2',
                                                        'option' => 'option2',
                                                        'is_correct_answer' => false,
                                                    ]
                                                ]
                                        ]
                                    ]
                            ]
                    ]
                ]
        ];
        $this->apiSignIn($contentAuthor1);

        $response = $this->putJson(
            "/api/v1/en/content-author/subjects/resources/{$resourceSection1->id}/fill",
            $requestData
        );
        $response->assertOk();
        $response->assertJsonStructure(
            [
                'data' =>
                    [
                        'type',
                        'id',
                        'attributes' =>
                            [
                                'resource_id',
                                'resource_slug',
                                'is_active',
                                'is_editable',
                            ],
                        'relationships' =>
                            [
                                'learningResourceAcceptCriteria' =>
                                    [
                                        'data' =>
                                            [
                                                'type',
                                                'id',
                                            ]
                                    ],
                                'resourceSubjectFormatSubjectData' =>
                                    [
                                        'data' =>
                                            [
                                                'type',
                                                'id',
                                            ]
                                    ]
                            ]
                    ],
                'included' =>
                    [

                        [
                            'type',
                            'id',
                            'attributes' =>
                                [
                                    'due_date',
                                    'description',
                                ]
                        ],
                        [
                            'type',
                            'id',
                            'attributes' =>
                                [
                                    'description',
                                    'questions' =>
                                        [
                                            [
                                                'id',
                                                'question',
                                                'options' =>
                                                    [
                                                        [
                                                            'id',
                                                            'option',
                                                            'is_correct_answer',
                                                        ],
                                                        [
                                                            'id',
                                                            'option',
                                                            'is_correct_answer',
                                                        ]
                                                    ]
                                            ]
                                        ]
                                ]
                        ]
                    ]
            ]
        );
    }

    public function test_fill_resource_flash()
    {
        dump('test_fill_resource_flash');

        $this->disableExceptionHandling();

        // sign in as SME and creating a subject
        $sme = $this->authSME();

        $subject = factory(Subject::class)->create();

        $subject->update(['sme_id' => $sme->id]);

        $contentAuthor1 = $this->authContentAuthor();
        $contentAuthor2 = $this->authContentAuthor();
        $subject->contentAuthors()->attach([
            $contentAuthor1->id,
            $contentAuthor2->id,
        ]);

        // resource data
        $resourceData = [
            'title' => LearningResourcesEnums::FLASH,
            'slug' => LearningResourcesEnums::FLASH,
        ];
        $resource = factory(Resource::class)->create($resourceData);

        $section1 = factory(SubjectFormatSubject::class)->create();
        $resourceSection1 = factory(ResourceSubjectFormatSubject::class)->create([
            'subject_format_subject_id' => $section1->id,
            'resource_id' => $resource->id,
            'resource_slug' => $resource->slug,
        ]);

        $taskResourceSection1 = factory(Task::class)->create([
            'subject_id' => $subject->id,
            'resource_subject_format_subject_id' => $resourceSection1->id,
            'subject_format_subject_id' => $section1->id,
            'is_assigned' => 0,
        ]);

        ContentAuthorTask::create([
            'task_id' => $taskResourceSection1->id,
            'content_author_id' => $contentAuthor1->contentAuthor->id,
        ]);
        // uploading a file
        $file = UploadedFile::fake()->image('avatar.jpg');
        $fileResponse = $this->json('post', '/api/v1/en/image', ['images' => [$file]], $this->loginUsingHeader($contentAuthor1));
        $fileContent = json_decode($fileResponse->getContent());
        // getting the file ID
        $myFileId = 0;
        foreach ($fileContent as $content) {
            foreach ($content as $c) {
                // getting the id of one file
                $myFileId = $c->id;
            };
        }
        $requestData =  [
            'data' =>
                [
                    'type' => 'resource_subject_format_subject',
                    'id' => $taskResourceSection1->id,
                    'attributes' =>
                        [
                            'resource_id' => '7',
                            'resource_slug' => 'flash',
                            'is_active' => true,
                            'is_editable' => true,
                        ],
                    'relationships' =>
                        [
                            'learningResourceAcceptCriteria' =>
                                [
                                    'data' =>
                                        [
                                            'type' => 'learning_resource_accept_criteria_field',
                                            'id' => '1dd450e0-682b-4c2d-82d9-64e380e2f775',
                                        ],
                                ],
                            'resourceSubjectFormatSubjectData' =>
                                [
                                    'data' =>
                                        [
                                            'type' => 'resource_subject_format_subject_data',
                                            'id' => 'new',
                                        ],
                                ],
                        ],
                ],
            'included' =>
                [
                        [
                            'type' => 'learning_resource_accept_criteria_field',
                            'id' => '1dd450e0-682b-4c2d-82d9-64e380e2f775',
                            'attributes' =>
                                [
                                    'due_date' => 4,
                                    'description' => 'flash description',
                                    'flash' => 'file',
                                ],
                        ],
                        [
                            'type' => 'resource_subject_format_subject_data',
                            'id' => 'new',
                            'attributes' =>
                                [
                                    'description' => 'flash description',
                                    'flash' => 'file',
                                ],
                            'relationships' =>
                                [
                                    'attachMedia' =>
                                        [
                                            'data' =>
                                                [
                                                    'type' => 'attach_media',
                                                    'id' => $myFileId,
                                                ],
                                        ],
                                ],
                        ],
                        [
                            'type' => 'attach_media',
                            'id' => $myFileId,
                            'attributes' =>
                                [
                                    'description' => 'geek'
                                ],
                        ],
                ],
        ];
        $this->apiSignIn($contentAuthor1);

        $response = $this->putJson(
            "/api/v1/en/content-author/subjects/resources/{$resourceSection1->id}/fill",
            $requestData
        );
        $response->assertOk();

        $response->assertJsonStructure(
            [
                'data' =>
                    [
                        'type',
                        'id',
                        'attributes' =>
                            [
                                'resource_id',
                                'resource_slug',
                                'is_active',
                                'is_editable',
                            ],
                        'relationships' =>
                            [
                                'learningResourceAcceptCriteria' =>
                                    [
                                        'data' =>
                                            [
                                                'type',
                                                'id',
                                            ]
                                    ]
                            ]
                    ],
                'included' =>
                    [
                        //this is will assert the attach media
                        [
                            'type',
                            'id',
                            'attributes' =>
                                [
                                    'url'
                                ]
                        ],
                        [
                            'type',
                            'id',
                            'attributes' =>
                                [
                                    'due_date',
                                    'description',
                                ]
                        ],
                    ]
            ]
        );

        $flashData = FlashData::where('resource_subject_format_subject_id', $resourceSection1->id)->first();
        deleteMedia($flashData->media()->pluck('id')->toArray(),
                    $flashData->media());
    }

    public function test_fill_resource_picture()
    {
        dump('test_fill_resource_picture');
        $this->disableExceptionHandling();

        // sign in as SME and creating a subject
        $sme = $this->authSME();

        $subject = factory(Subject::class)->create();

        $subject->update(['sme_id' => $sme->id]);

        $contentAuthor1 = $this->authContentAuthor();
        $contentAuthor2 = $this->authContentAuthor();
        $subject->contentAuthors()->attach([
            $contentAuthor1->id,
            $contentAuthor2->id,
        ]);

        // resource data
        $resourceData = [
            'title' => LearningResourcesEnums::PICTURE,
            'slug' => LearningResourcesEnums::PICTURE,
        ];
        $resource = factory(Resource::class)->create($resourceData);

        $section1 = factory(SubjectFormatSubject::class)->create();
        $resourceSection1 = factory(ResourceSubjectFormatSubject::class)->create([
            'subject_format_subject_id' => $section1->id,
            'resource_id' => $resource->id,
            'resource_slug' => $resource->slug,
        ]);

        $taskResourceSection1 = factory(Task::class)->create([
            'subject_id' => $subject->id,
            'resource_subject_format_subject_id' => $resourceSection1->id,
            'subject_format_subject_id' => $section1->id,
            'is_assigned' => 0,
        ]);

        ContentAuthorTask::create([
            'task_id' => $taskResourceSection1->id,
            'content_author_id' => $contentAuthor1->contentAuthor->id,
        ]);

        // uploading a file
        $file = UploadedFile::fake()->image('avatar.jpg');
        $fileResponse = $this->json('post', '/api/v1/en/image', ['images' => [$file]], $this->loginUsingHeader($contentAuthor1));
        $fileContent = json_decode($fileResponse->getContent());
        // getting the file ID
        $myFileId = 0;
        foreach ($fileContent as $content) {
            foreach ($content as $c) {
                // getting the id of one file
                $myFileId = $c->id;
            };
        }

        $requestData = [
           "data" => [
                 "type" => "resource_subject_format_subject",
                 "id" => "505",
                 "attributes" => [
                    "resource_id" => "8",
                    "resource_slug" => "picture",
                    "is_active" => true,
                    "is_editable" => true
                 ],
                 "relationships" => [
                       "resourceSubjectFormatSubjectData" => [
                          "data" => [
                             "type" => "resource_subject_format_subject_data",
                             "id" => "new_1"
                          ]
                       ],
                       "attachMedia" => [
                                "data" => [
                                   [
                                      "type" => "attach_media",
                                      "id" => $myFileId
                                   ]
                                ]
                             ]
                    ]
              ],
           "included" => [
                         [
                            "type" => "resource_subject_format_subject_data",
                            "id" => "new_1",
                            "attributes" => [
                               "description" => "test picture des"
                            ]
                         ],

                        [
                          "type" => "attach_media",
                          "id" => $myFileId,
                          "attributes" => [
                                "description" => "picture desc"
                          ]
                       ]
                  ]
        ];

        $this->apiSignIn($contentAuthor1);

        $response = $this->putJson(
            "/api/v1/en/content-author/subjects/resources/{$resourceSection1->id}/fill",
            $requestData
        );
        $response->assertOk();

        $response->assertJsonStructure(
            [
                'data' =>
                    [
                        'type',
                        'id',
                        'attributes' =>
                            [
                                'resource_id',
                                'resource_slug',
                                'is_active',
                                'is_editable',
                            ],
                        'relationships' =>
                            [
                                'learningResourceAcceptCriteria' =>
                                    [
                                        'data' =>
                                            [
                                                'type',
                                                'id',
                                            ]
                                    ]
                            ]
                    ],
                'included' =>
                    [
                        //this is will assert the attach media
                        [
                            'type',
                            'id',
                            'attributes' =>
                                [
                                    'url'
                                ]
                        ],
                        [
                            'type',
                            'id',
                            'attributes' =>
                                [
                                    'due_date',
                                    'description',
                                ]
                        ],
                    ]
            ]
        );

        $pictureData = PictureData::where('resource_subject_format_subject_id', $resourceSection1->id)->first();
        deleteMedia($pictureData->media()->pluck('id')->toArray(),
            $pictureData->media());
    }
    public function test_fill_resource_hot_spot()
    {
        dump('test_fill_resource_hot_spot');

        // sign in as SME and creating a subject
        $sme = $this->authSME();

        $subject = factory(Subject::class)->create();

        $subject->update(['sme_id' => $sme->id]);

        $contentAuthor1 = $this->authContentAuthor();
        $contentAuthor2 = $this->authContentAuthor();
        $subject->contentAuthors()->attach([
            $contentAuthor1->id,
            $contentAuthor2->id,
        ]);

        // resource data
        $resourceData = [
            'title' => LearningResourcesEnums::HOTSPOT,
            'slug' => LearningResourcesEnums::HOTSPOT,
        ];
        $resource = factory(Resource::class)->create($resourceData);

        $section1 = factory(SubjectFormatSubject::class)->create();
        $resourceSection1 = factory(ResourceSubjectFormatSubject::class)->create([
            'accept_criteria' => '{"description":"HotSpot test","difficulty_level":"1","learning_outcome":"2","number_of_question":2}',
            'subject_format_subject_id' => $section1->id,
            'resource_id' => $resource->id,
            'resource_slug' => $resource->slug,
        ]);

        $taskResourceSection1 = factory(Task::class)->create([
            'subject_id' => $subject->id,
            'resource_subject_format_subject_id' => $resourceSection1->id,
            'subject_format_subject_id' => $section1->id,
            'is_assigned' => 0,
        ]);

        ContentAuthorTask::create([
            'task_id' => $taskResourceSection1->id,
            'content_author_id' => $contentAuthor1->contentAuthor->id,
        ]);

        // uploading a file
        $file = UploadedFile::fake()->image('avatar.jpg');
        $fileResponse = $this->json('post', '/api/v1/en/image', ['images' => [$file]], $this->loginUsingHeader($contentAuthor1));
        $fileContent = json_decode($fileResponse->getContent());
        // getting the file ID
        $myFileId = 0;
        foreach ($fileContent as $content) {
            foreach ($content as $c) {
                // getting the id of one file
                $myFileId = $c->id;
            };
        }

        $requestData = [
            'data' =>
                [
                    'type' => 'resource_subject_format_subject',
                    'id' => $resourceSection1->id,
                    'attributes' =>
                        [
                            'resource_id' => '13',
                            'resource_slug' => 'hotspot',
                            'is_active' => true,
                            'is_editable' => true,
                        ],
                    'relationships' =>
                        [
                            'resourceSubjectFormatSubjectData' =>
                                [
                                    'data' =>
                                        [
                                            'type' => 'resource_subject_format_subject_data',
                                            'id' => 'new',
                                        ],
                                ]
                        ],
                ],
            'included' =>
                [
                    [
                        'type' => 'resource_subject_format_subject_data',
                        'id' => 'new',
                        'attributes' =>
                            [
                                'description' => 'greek',
                                "questions" => [
                                   [
                                      "id" => "new_x",
                                      "question" => "q1",
                                      "answers" => [
                                            [
                                                "id" => "new_1" ,
                                                "answer" => "a1"
                                            ]
                                      ]
                                   ]
                                ]
                            ]
                     ]
                 ]
        ];
        $this->apiSignIn($contentAuthor1);

        $response = $this->putJson(
            "/api/v1/en/content-author/subjects/resources/{$resourceSection1->id}/fill",
            $requestData
        );
        $response->assertOk();
//        dd($response->dump());
        $response->assertJsonStructure(
            [
                'data' =>
                    [
                        'type',
                        'id',
                        'attributes' =>
                            [
                                'resource_id',
                                'resource_slug',
                                'is_active',
                                'is_editable',
                            ],
                        'relationships' =>
                            [
                                'learningResourceAcceptCriteria' =>
                                    [
                                        'data' =>
                                            [
                                                'type',
                                                'id',
                                            ]
                                    ],
                                "resourceSubjectFormatSubjectData" =>
                                    [
                                        "data" =>
                                            [
                                                "type" ,
                                                "id"
                                            ]
                                    ]
                            ]
                    ],
                'included' =>
                    [
                        [
                            'type',
                            'id',
                            'attributes' =>
                                [
                                    "description",
                                    "difficulty_level",
                                    "learning_outcome",
                                    "number_of_question"
                                ]
                        ],
                        [
                            'type',
                            'id',
                            'attributes' =>
                                [
                                    "description",
                                    "questions" => [
                                        [
                                            "id",
                                            "question",
                                            "answers" => [
                                                [
                                                    'id' ,
                                                    'answer'
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                        ],
                    ]
            ]
        );

        $hotSpotData = HotSpotData::where('resource_subject_format_subject_id', $resourceSection1->id)->first();
        deleteMedia($hotSpotData->media()->pluck('id')->toArray(),
            $hotSpotData->media());
    }

    public function test_fill_resource_complete()
    {
        dump('test_fill_resource_complete');
        $this->disableExceptionHandling();

        $sme = $this->authSME();

        $subject = factory(Subject::class)->create();

        $subject->update(['sme_id' => $sme->id]);

        $contentAuthor1 = $this->authContentAuthor();
        $contentAuthor2 = $this->authContentAuthor();
        $subject->contentAuthors()->attach([
            $contentAuthor1->id,
            $contentAuthor2->id,
        ]);


        $resourceData = [
            'title' => LearningResourcesEnums::COMPLETE,
            'slug' => LearningResourcesEnums::COMPLETE,
        ];
        $resource = factory(Resource::class)->create($resourceData);

        $section1 = factory(SubjectFormatSubject::class)->create();
        $resourceSection1 = factory(ResourceSubjectFormatSubject::class)->create([
            'subject_format_subject_id' => $section1->id,
            'resource_id' => $resource->id,
            'resource_slug' => $resource->slug,
        ]);

        $taskResourceSection1 = factory(Task::class)->create([
            'subject_id' => $subject->id,
            'resource_subject_format_subject_id' => $resourceSection1->id,
            'subject_format_subject_id' => $section1->id,
            'is_assigned' => 0,
        ]);

        ContentAuthorTask::create([
            'task_id' => $taskResourceSection1->id,
            'content_author_id' => $contentAuthor1->contentAuthor->id,
        ]);


        $requestData = [
            'data' =>
                [
                    'type' => 'resource_subject_format_subject',
                    'id' => "{$taskResourceSection1->id}",
                    'attributes' =>
                        [
                            'resource_id' => '9',
                            'resource_slug' => 'complete',
                            'is_active' => true,
                            'is_editable' => true,
                        ],
                    'relationships' =>
                        [
                            'learningResourceAcceptCriteria' =>
                                [
                                    'data' =>
                                        [
                                            'type' => 'learning_resource_accept_criteria_field',
                                            'id' => '1dd450e0-682b-4c2d-82d9-64e380e2f7d5',
                                        ]
                                ],
                            'resourceSubjectFormatSubjectData' =>
                                [
                                    'data' =>
                                        [
                                            'type' => 'resource_subject_format_subject_data',
                                            'id' => 'new',
                                        ]
                                ]
                        ]
                ],
            'included' =>
                [
                    [
                        'type' => 'learning_resource_accept_criteria_field',
                        'id' => '1dd450e0-682b-4c2d-82d9-64e380e2f7d5',
                        'attributes' =>
                            [
                                'due_date' => 4,
                                'description' => 'Testt',
                                'matching_type' => '9',
                            ]
                    ],
                    [
                        'type' => 'resource_subject_format_subject_data',
                        'id' => 'new',
                        'attributes' =>
                            [
                                'description' => 'description',
                                'questions' =>
                                    [
                                        [
                                            'id' => 'new',
                                            'question' => 'q1',
                                            'answer'    =>  'answer1',
                                            'accepted_answers' => [
                                                [
                                                    'id' => 'new_answer1',
                                                    'answer'    =>  'new_answer1'
                                                ],
                                                [
                                                    'id' => 'new_answer2',
                                                    'answer'    =>  'new_answer2'
                                                ],
                                            ]
                                        ]
                                    ] ,
                            ]
                    ]
                ]
        ];
        $this->apiSignIn($contentAuthor1);

        $response = $this->putJson(
            "/api/v1/en/content-author/subjects/resources/{$resourceSection1->id}/fill",
            $requestData
        )
            ->assertOk();
        $response->assertJsonStructure(
            [
                'data' =>
                    [
                        'type',
                        'id',
                        'attributes' =>
                            [
                                'resource_id',
                                'resource_slug',
                                'is_active',
                                'is_editable',
                            ],
                        'relationships' =>
                            [
                                'learningResourceAcceptCriteria' =>
                                    [
                                        'data' =>
                                            [
                                                'type',
                                                'id',
                                            ]
                                    ],
                                'resourceSubjectFormatSubjectData' =>
                                    [
                                        'data' =>
                                            [
                                                'type',
                                                'id',
                                            ]
                                    ]
                            ]
                    ],
                'included' =>
                    [
                        [
                            "type",
                            "id",
                            "attributes" => [
                                "question",
                                "answer",
                                "accepted_answers" => [
                                    [

                                        "id",
                                        "answer"
                                    ],
                                    [
                                        "id",
                                        "answer"
                                    ]
                                ]
                            ]
                        ],
                    ]
                ]


        );

        $completeData = [
            'description' => 'description',
            'resource_subject_format_subject_id' => $resourceSection1->id,

        ];

        $this->assertDatabaseHas('res_complete_question_data', $completeData);

        $completeData = CompleteData::where($completeData)->first();

        $completeQuestionsData = [
            'question' => 'q1',
            'res_complete_data_id' => $completeData->id,
        ];

        $this->assertDatabaseHas('res_complete_questions', $completeQuestionsData);

        $completeQuestion = CompleteQuestion::where($completeQuestionsData)->first();


        $mainAnswer = [
            'answer' => 'answer1',
            'res_complete_question_id' => $completeQuestion->id,
        ];

        $this->assertDatabaseHas('res_complete_answers', $mainAnswer);

        $acceptedAnswer1 = [
            'answer' => 'new_answer1',
            'res_complete_question_id' => $completeQuestion->id,
        ];

        $this->assertDatabaseHas('res_complete_accepted_answers', $acceptedAnswer1);

        $acceptedAnswer2 = [
            'answer' => 'new_answer2',
            'res_complete_question_id' => $completeQuestion->id,
        ];

        $this->assertDatabaseHas('res_complete_accepted_answers', $acceptedAnswer2);
    }
}
