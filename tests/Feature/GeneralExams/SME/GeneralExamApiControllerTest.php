<?php

namespace Tests\Feature\GeneralExams\SME;

use Tests\TestCase;
use App\OurEdu\Users\User;
use App\OurEdu\Options\Option;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\GeneralExams\GeneralExam;
use App\OurEdu\Options\Enums\OptionsTypes;
use Illuminate\Foundation\Testing\WithFaker;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use App\OurEdu\GeneralExams\Models\PreparedGeneralExamQuestion;
use App\OurEdu\ResourceSubjectFormats\Models\Complete\CompleteData;
use App\OurEdu\ResourceSubjectFormats\Models\Complete\CompleteQuestion;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;

class GeneralExamApiControllerTest extends TestCase
{
    use WithFaker;

    public function test_sme_store_general_exam()
    {
        $this->disableExceptionHandling();

        dump('test_sme_store_general_exam');

        $sme = create(User::class, ['type' => UserEnums::SME_TYPE]);
        $subject = create(Subject::class, ['sme_id' => $sme->id]);
        $difficultyLevel = Option::whereType(OptionsTypes::RESOURCE_DIFFICULTY_LEVEL)->first() ?? create(Option::class)->create(['type' => OptionsTypes::RESOURCE_DIFFICULTY_LEVEL]);
        $sections = create(SubjectFormatSubject::class, ['subject_id' => $subject->id], 2);

        $now = now()->addMonth();

        $request_data = [
            'data'  => [
                'id' => null,
                'type'  =>  'general_exam',
                'attributes'    =>  [
                    'name'  =>  $this->faker->name(),
                    'date'  =>  $now->format('Y-m-d'),
                    'start_time'    =>  $now->format('H:i:s'),
                    'end_time'  =>  $now->addHours(3)->format('H:i:s'),
                    'subject_id'    =>  $subject->id,
                    'subject_format_subjects'    =>  $sections->pluck('id')->toArray(),
                    'difficulty_level_id'   =>  $difficultyLevel->id
                ]
            ]
        ];

        $response = $this->postJson(route('api.sme.general_exams.storeGeneralExam', ['language' => 'en']), $request_data, $this->loginUsingHeader($sme))
            ->assertOk()
            ->assertJsonFragment(['message' => trans('api.General exam created')])
            ->assertJsonStructure([
                "data" => [
                ]
            ]);
    }

    public function test_list_general_exams()
    {
        $this->disableExceptionHandling();

        dump('test_list_general_exams');

        $sme = create(User::class, ['type' => UserEnums::SME_TYPE]);
        $subject = create(Subject::class, ['sme_id' => $sme->id]);
        $difficultyLevel = Option::whereType(OptionsTypes::RESOURCE_DIFFICULTY_LEVEL)->first() ?? create(Option::class)->create(['type' => OptionsTypes::RESOURCE_DIFFICULTY_LEVEL]);
        $sections = create(SubjectFormatSubject::class, ['subject_id' => $subject->id], 2);

        create(GeneralExam::class, [
            'subject_id'    =>  $subject->id,
            'difficulty_level_id'    =>  $difficultyLevel->id,
            'subject_format_subjects'    =>  json_encode($sections->pluck('id')->toArray()),
        ], 2);


        $response = $this->getJson(route('api.sme.general_exams.index', ['language' => 'en']), $this->loginUsingHeader($sme))
            ->assertOk()
            ->assertJsonFragment(['total' => 2])
            ->assertJsonStructure([
                "data" => [
                ]
            ]);
    }

    public function test_view_general_exam()
    {
        $this->disableExceptionHandling();

        dump('test_view_general_exam');

        $sme = create(User::class, ['type' => UserEnums::SME_TYPE]);
        $subject = create(Subject::class, ['sme_id' => $sme->id]);
        $difficultyLevel = Option::whereType(OptionsTypes::RESOURCE_DIFFICULTY_LEVEL)->first() ?? create(Option::class)->create(['type' => OptionsTypes::RESOURCE_DIFFICULTY_LEVEL]);
        $sections = create(SubjectFormatSubject::class, ['subject_id' => $subject->id], 2);

        $exam = create(GeneralExam::class, [
            'subject_id'    =>  $subject->id,
            'difficulty_level_id'    =>  $difficultyLevel->id,
            'subject_format_subjects'    =>  json_encode($sections->pluck('id')->toArray()),
        ]);


        $response = $this->getJson(route('api.sme.general_exams.view', ['language' => 'en', 'exam' => $exam]), $this->loginUsingHeader($sme))
            ->assertOk()
            ->assertJsonStructure([
                "data" => [
                ]
            ]);
    }

    public function test_update_general_exam()
    {
        $this->disableExceptionHandling();

        dump('test_update_general_exam');

        $sme = create(User::class, ['type' => UserEnums::SME_TYPE]);
        $subject = create(Subject::class, ['sme_id' => $sme->id]);
        $difficultyLevel = Option::whereType(OptionsTypes::RESOURCE_DIFFICULTY_LEVEL)->first() ?? create(Option::class)->create(['type' => OptionsTypes::RESOURCE_DIFFICULTY_LEVEL]);
        $sections = create(SubjectFormatSubject::class, ['subject_id' => $subject->id], 2);

        $now = now()->addMonth();

        $request_data = [
            'data'  => [
                'id' => null,
                'type'  =>  'general_exam',
                'attributes'    =>  [
                    'name'  =>  $name = $this->faker->name(),
                    'date'  =>  $now->format('Y-m-d'),
                    'start_time'    =>  $now->format('H:i:s'),
                    'end_time'  =>  $now->addHours(3)->format('H:i:s'),
                    'subject_id'    =>  $subject->id,
                    'subject_format_subjects'    =>  $sections->pluck('id')->toArray(),
                    'difficulty_level_id'   =>  $difficultyLevel->id
                ]
            ]
        ];

        $exam = create(GeneralExam::class, ['subject_id' => $subject->id]);

        $response = $this->postJson(route('api.sme.general_exams.update', ['language' => 'en', 'exam' => $exam]), $request_data, $this->loginUsingHeader($sme))
            ->assertOk()
            ->assertJsonFragment(['message' => trans('api.General exam updated')])
            ->assertJsonStructure([
                "data" => [
                ]
            ]);

        $this->assertSame($name, $exam->fresh()->name);
    }

    public function test_update_general_exam_questions()
    {
        $this->disableExceptionHandling();

        dump('test_update_general_exam_questions');

        $sme = create(User::class, ['type' => UserEnums::SME_TYPE]);
        $subject = create(Subject::class, ['sme_id' => $sme->id]);
        $sections = create(SubjectFormatSubject::class, ['subject_id' => $subject->id], 2);

        $exam = create(GeneralExam::class, [
            'subject_id'    =>  $subject->id,
            'subject_format_subjects'    =>  json_encode($sections->pluck('id')->toArray()),
        ]);

        $sections->each(function ($section) {
            $resource = create(ResourceSubjectFormatSubject::class, ['subject_format_subject_id' => $section->id]);

            $data = factory(CompleteData::class)->create(['resource_subject_format_subject_id' => $resource->id]);

            create(CompleteQuestion::class, ['res_complete_data_id' => $data->id], 5);
        });

        $request_data = [
            'data'  => [
                'id' => null,
                'type'  =>  'general_exam',
                'attributes'    =>  [
                    'prepared_questions' => PreparedGeneralExamQuestion::where('subject_id', $subject->id)->pluck('id')->toArray()
                ]
            ]
        ];

        $exam = create(GeneralExam::class, ['subject_id' => $subject->id, 'subject_format_subjects' => json_encode($sections->pluck('id')->toArray())]);

        $response = $this->postJson(route('api.sme.general_exams.updateQuestions', ['language' => 'en', 'exam' => $exam]), $request_data, $this->loginUsingHeader($sme))
            ->assertOk()
            ->assertJsonFragment(['message' => trans('api.Prepared general exam questions updated')]);
    }

    public function test_sme_can_delete_general_exam()
    {
        $this->disableExceptionHandling();

        dump('test_sme_can_delete_general_exam');

        $sme = create(User::class, ['type' => UserEnums::SME_TYPE]);
        $subject = create(Subject::class, ['sme_id' => $sme->id]);

        $exam = create(GeneralExam::class, [
            'subject_id'    =>  $subject->id,
        ]);

        $response = $this->getJson(route('api.sme.general_exams.delete', ['language' => 'en', 'exam' => $exam]), $this->loginUsingHeader($sme))
            ->assertOk()
            ->assertJsonFragment(['message' => trans('api.Deleted Successfully')]);
    }

    public function test_sme_publishes_a_general_exam()
    {
        $this->disableExceptionHandling();

        dump('test_sme_publishes_a_general_exam');

        $sme = create(User::class, ['type' => UserEnums::SME_TYPE]);

        $subject = create(Subject::class, ['sme_id' => $sme->id]);

        $sections = create(SubjectFormatSubject::class, ['subject_id' => $subject->id], 2);

        $exam = create(GeneralExam::class, [
            'subject_id'    =>  $subject->id,
            'subject_format_subjects'    =>  json_encode($sections->pluck('id')->toArray()),
        ]);

        $preparedQuestions = PreparedGeneralExamQuestion::take(2)->get();

        $exam->preparedQuestions()->sync($preparedQuestions);

        $response = $this->getJson(route('api.sme.general_exams.publish', ['language' => 'en', 'exam' => $exam]), $this->loginUsingHeader($sme))
            ->assertOk()
            ->assertJsonFragment(['message' => trans('api.Published Successfully')]);
    }

    public function test_sme_list_general_exam_sections()
    {
        $this->disableExceptionHandling();

        dump('test_sme_list_general_exam_sections');

        $sme = create(User::class, ['type' => UserEnums::SME_TYPE]);
        $subject = create(Subject::class, ['sme_id' => $sme->id]);
        $sections = create(SubjectFormatSubject::class, ['subject_id' => $subject->id], 2);

        $response = $this->getJson(route('api.sme.general_exams.getSubjectSections', ['language' => 'en', 'subjectId' => $subject->id]), $this->loginUsingHeader($sme))
            ->assertOk()
            ->assertJsonStructure([
                "data" => [
                ]
            ]);
    }

    public function test_sme_show_section_prapred_general_exam_questions()
    {
        $this->disableExceptionHandling();

        dump('test_sme_show_section_prapred_general_exam_questions');

        $sme = create(User::class, ['type' => UserEnums::SME_TYPE]);

        $subject = create(Subject::class, ['sme_id' => $sme->id]);

        $section = create(SubjectFormatSubject::class, ['subject_id' => $subject->id]);

        $response = $this->getJson(route('api.sme.general_exams.getSectionQuestions', ['language' => 'en', 'sectionId' => $section->id]), $this->loginUsingHeader($sme))
            ->assertOk()
            ->assertJsonStructure([
                "data" => [
                ]
            ]);
    }
}
