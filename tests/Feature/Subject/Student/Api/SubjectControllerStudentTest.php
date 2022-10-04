<?php

namespace Tests\Feature\Subject\Student\Api;

use Tests\TestCase;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\Reports\ReportEnum;
use App\OurEdu\Subscribes\Subscribe;
use App\OurEdu\Subjects\Models\Subject;
use Illuminate\Foundation\Testing\WithFaker;
use App\OurEdu\Invitations\Models\ParentStudent;
use App\OurEdu\Subjects\Models\SubModels\SubjectMedia;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use App\OurEdu\ResourceSubjectFormats\Models\Progress\SubjectFormatProgressStudent;

class SubjectControllerStudentTest extends TestCase
{
    use WithFaker;
    /**
     * A basic test example.
     *
     * @return void
     */


    public function test_get_list_subject()
    {
        dump('test_get_list_subject');

        $student = $this->authStudent();
        $this->apiSignIn($student);

        $this->getJson("/api/v1/en/student/subjects")->assertOk();
    }

    public function test_student_subscribe_subject()
    {
        dump('test_student_subscribe_subject');

        $student = $this->authStudent();
        $this->apiSignIn($student);
        $subject = factory(Subject::class)->make()->toArray();
        $subject['grade_class_id'] = $student->student->class_id;
        $subject['educational_system_id'] = $student->student->educational_system_id;
        $subject['academical_years_id'] = $student->student->academical_year_id;
        $subject['country_id'] = $student->country_id;
        $subject = Subject::create($subject);
        $student = $student->student;
        $student->wallet_amount = $subject->subscription_cost;
        $student->save();
        $response = $this->postJson("/api/v1/en/student/subjects/subscribe/" . $subject->id, []);
        $response->assertOk();

        $this->assertDatabaseHas('subject_subscribe_students', [
            'subject_id' => $subject->id,
            'student_id' => $student->id,
        ]);
    }

    public function test_student_report_subject()
    {
        dump('test_student_report_subject');

        $student = $this->authStudent();
        $this->apiSignIn($student);
        $subject = factory(Subject::class)->make()->toArray();
        $subject['grade_class_id'] = $student->student->class_id;
        $subject['educational_system_id'] = $student->student->educational_system_id;
        $subject['academical_years_id'] = $student->student->academical_year_id;
        $subject['country_id'] = $student->country_id;
        $subject = Subject::create($subject);
        $sme = $this->authSME();
        $subject->update(['sme_id' => $sme->id]);
        $data = [
            'data' => [
                'type' => 'report',
                'id'   => null,
                'attributes' => [
                    'report' => 'test Report'
                ]
            ]
        ];
        $this->postJson("/api/v1/en/student/reports/".$subject->id."/subject/" . $subject->id, $data)->assertOk();

        $this->assertDatabaseHas('student_reports', [
            'student_id' => $student->student->id,
            'reportable_id' => $subject->id,
            'reportable_type' => ReportEnum::getType('subject'),
            'report' => 'test Report'
        ]);
    }

    public function test_student_list_subjects_with_progress()
    {
        dump('test_student_list_subjects_with_progress');

        $student = $this->authStudent();
        $this->apiSignIn($student);
        $subject = factory(Subject::class)->make()->toArray();
        $subject['grade_class_id'] = $student->student->class_id;
        $subject['educational_system_id'] = $student->student->educational_system_id;
        $subject['academical_years_id'] = $student->student->academical_year_id;
        $subject['country_id'] = $student->country_id;
        $subject['total_points'] = 1000;
        $subject = factory(Subject::class)->create($subject);
        Subscribe::create([
            'subject_id' => $subject->id,
            'student_id' => $student->student->id,
        ]);
        SubjectFormatProgressStudent::create([
            'subject_id' => $subject->id,
            'student_id' => $student->student->id,
            'points' => 500
        ]);
        $response = $this->getJson('/api/v1/en/student/subjects');
        $response->assertJsonStructure(
            [
                'data' =>
                    [
                        [
                            'type',
                            'id',
                            'attributes' =>
                                [
                                    'name',
                                    'section_type',
                                    'progress'
                                ],
                        ],
                    ],
                'included' =>
                    []
            ]
        );
    }

    public function test_student_list_subjects_with_progress_by_parent()
    {
        dump('test_student_list_subjects_with_progress_by_parent');

        $student = $this->authStudent();
        $parent = $this->authParent();
        $this->apiSignIn($parent);
        ParentStudent::insert([
            'parent_id' => $parent->id,
            'student_id' => $student->id
        ]);
        $subject = factory(Subject::class)->make()->toArray();
        $subject['grade_class_id'] = $student->student->class_id;
        $subject['educational_system_id'] = $student->student->educational_system_id;
        $subject['academical_years_id'] = $student->student->academical_year_id;
        $subject['country_id'] = $student->country_id;
        $subject['total_points'] = 1000;
        $subject = factory(Subject::class)->create($subject);
        Subscribe::create([
            'subject_id' => $subject->id,
            'student_id' => $student->student->id,
        ]);
        SubjectFormatProgressStudent::create([
            'subject_id' => $subject->id,
            'student_id' => $student->student->id,
            'points' => 500
        ]);
        $response = $this->getJson('/api/v1/en/student/subjects/list-subjects/'.$student->student->id);
        $response->assertJsonStructure(
            [
                'data' =>
                    [
                        [
                            'type',
                            'id',
                            'attributes' =>
                                [
                                    'name',
                                    'section_type',
                                    'progress'
                                ]
                        ],
                    ],
                'included' =>
                    []
            ]
        );
    }

    public function test_view_subject_with_inactive_sections()
    {
        dump('test_view_subject_with_inactive_sections');
        $student = $this->authStudent();
        $this->apiSignIn($student);
        $subject = factory(Subject::class)->make()->toArray();
        $subject['grade_class_id'] = $student->student->class_id;
        $subject['is_active'] = 1;
        $subject['educational_system_id'] = $student->student->educational_system_id;
        $subject['academical_years_id'] = $student->student->academical_year_id;
        $subject['country_id'] = $student->country_id;
        $subject = Subject::create($subject);
        $student = $student->student;
        $student->wallet_amount = $subject->subscription_cost;
        $student->save();

        // subscribe to the subject
        Subscribe::create([
            'subject_id' => $subject->id,
            'student_id' => $student->id,
        ]);
        $ActiveSubjectFormatData = factory(SubjectFormatSubject::class)->make()->toArray();
        $ActiveSubjectFormatData['subject_id'] = $subject->id;
        $ActiveSubjectFormatData['title'] = 'Active Section';
        $ActiveSubjectFormat = factory(SubjectFormatSubject::class)->create($ActiveSubjectFormatData);
        $inActiveSubjectFormatData = factory(SubjectFormatSubject::class)->make()->toArray();
        $inActiveSubjectFormatData['title'] = 'In Active Section';
        $inActiveSubjectFormatData['is_active'] = 0;
        $inActiveSubjectFormatData['subject_id'] = $subject->id;
        $inActiveSubjectFormat = factory(SubjectFormatSubject::class)->create($inActiveSubjectFormatData);

        $response = $this->getJson("api/v1/en/student/subjects/view-subject/".$subject->id);
        $response->assertJsonStructure(
            [
                 "data" => [
                     "type",
                     "id",
                     "attributes" => [
                         "name",
                         "educational_system",
                         "academical_years",
                         "grade_class",
                         "subscription_cost",
                         "subject_library_text"
                     ],
                     "relationships" => [
                         "actions" => [
                             "data" => [
                                 [
                                     "type",
                                     "id"
                                 ]
                             ]
                         ],
                         "subjectFormatSubjects" => [
                             "data" => [
                                 [
                                     "type",
                                     "id"
                                 ]
                             ]
                         ]
                     ]
                 ],
                 "included" => [
                     [
                         "type",
                         "id",
                         "attributes" => [
                             "endpoint_url",
                             "method",
                             "label",
                             "bg_color",
                             "key"
                         ]
                     ],
                     [
                         "type",
                         "id",
                         "attributes" => [
                             "endpoint_url",
                             "method",
                             "label",
                             "bg_color",
                             "key"
                         ]
                     ],
                     [
                         "type",
                         "id",
                         "attributes" => [
                             "title"
                         ],
                         "relationships" => [
                             "actions" => [
                                 "data" => [
                                     [
                                         "type",
                                         "id"
                                     ]
                                 ]
                             ]
                         ]
                     ]
                 ]
             ]
        );
    }

    public function test_student_list_subject_media()
    {
        dump('test_student_list_subject_media');

        $this->disableExceptionHandling();

        $user = create(User::class, ['type' => UserEnums::PARENT_TYPE]);

        $subject = create(Subject::class);

        create(SubjectMedia::class, ['subject_id' => $subject->id], 2);


        $response = $this->getJson(route('api.student.subjects.view-subject-media', ['language' => 'en', 'subject_id' => $subject->id]), $this->loginUsingHeader($user))
            ->assertOk()
            ->assertJsonStructure([
                "data" => [
                    [
                        'type',
                        'attributes' => [
                            'extension',
                            'url'
                        ]
                    ]
                ]
            ]);
    }

    public function test_student_list_subject_setions()
    {
        dump('test_student_list_subject_setions');

        $this->disableExceptionHandling();

        $user = create(User::class, ['type' => UserEnums::PARENT_TYPE]);

        $subject = create(Subject::class);

        create(SubjectFormatSubject::class, ['subject_id' => $subject->id], 2);


        $response = $this->getJson(route('api.student.subjects.view-subject-sections', ['language' => 'en', 'subject_id' => $subject->id]), $this->loginUsingHeader($user))
            ->assertOk()
            ->assertJsonStructure([
                "data" => [
                        'type',
                        'attributes' => [
                            'name',
                            
                        ]
                    ]
            ]);
    }

    public function test_student_list_section_child_sections()
    {
        dump('test_student_list_section_child_sections');

        $this->disableExceptionHandling();

        $user = create(User::class, ['type' => UserEnums::PARENT_TYPE]);

        $section = create(SubjectFormatSubject::class);

        create(SubjectFormatSubject::class, ['parent_subject_format_id' => $section->id], 2);


        $response = $this->getJson(route('api.student.subjects.view-section-sections', ['language' => 'en', 'sectionId' => $section->id]), $this->loginUsingHeader($user))
            ->assertOk()
            ->assertJsonStructure([
                "data" => [
                        'type',
                        'attributes' => [
                            'title',
                            
                        ]
                    ]
            ]);
    }
}
