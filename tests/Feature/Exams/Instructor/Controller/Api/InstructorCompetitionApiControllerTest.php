<?php


namespace Tests\Feature\Exams\Instructor\Controller\Api;

use App\OurEdu\Exams\Enums\ExamTypes;
use App\OurEdu\Exams\Models\Exam;
use Tests\TestCase;
use App\OurEdu\Options\Option;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Options\Enums\OptionsTypes;
use Illuminate\Foundation\Testing\WithFaker;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseData;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseOption;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseQuestion;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;

class InstructorCompetitionApiControllerTest extends TestCase
{
    use WithFaker;

    public function test_generate_instructor_competition()
    {
        dump('test_generate_instructor_competition');

        $subject = factory(Subject::class)->create();
        $instructor = $this->authInstructor();

        $this->apiSignIn($instructor);

        $option = Option::where('type', OptionsTypes::RESOURCE_DIFFICULTY_LEVEL)->first();

        // create formats for subject
        $subjectFormats = factory(SubjectFormatSubject::class, 2)->create(['subject_id' => $subject->id]);

        // each format
        $subjectFormats->each(function ($subjectFormat) {
            // create a format resource
            $subjectFormatResource = factory(ResourceSubjectFormatSubject::class)->create(['subject_format_subject_id' => $subjectFormat->id]);

            // create data based on format resource
            $trueFalseData = factory(TrueFalseData::class)->create([
                'resource_subject_format_subject_id' => $subjectFormatResource->id
            ]);

            // create true false questsions related to data
            $trueFalseQuestions = factory(TrueFalseQuestion::class, random_int(10, 12))->create([
                'res_true_false_data_id' => $trueFalseData->id
            ]);


            // create true false options related to questions
            $trueFalseQuestions->each(function ($trueFalseQuestion) {
                // create options based on questions
                factory(
                    TrueFalseOption::class,
                    2
                )->create([
                    'res_true_false_question_id' => $trueFalseQuestion->id
                ]);
            });
        });

        $subject->preparedQuestions()->update([
                'difficulty_level' => $option->slug
            ]);


        $data = [
            'data' => [
                'type' => 'exam',
                'id' => 'new',
                'attributes' => [
                    'number_of_questions' => 20,
                    'difficulty_level' => $option->id,
                    'subject_id' => $subject->id,
                    'subject_format_subject_ids' => $subject->subjectFormatSubject()->pluck('id')->toArray()
                ]
            ]
        ];
        $response = $this->postJson('api/v1/en/instructor/instructor-competitions/generate-competition', $data)
            ->assertJsonFragment(['message' => trans('api.Competition generated')]);

        $this->assertDatabaseHas('exams', [
            'subject_id'    =>  $subject->id,
           'difficulty_level' => $option->slug
       ]);

        $this->assertDatabaseHas('exam_questions', [
            'subject_id' => $subject->id
        ]);
    }

    public function test_start_instructor_competition()
    {
        dump('test_start_instructor_competition');
        $instructor = $this->authInstructor();

        $this->apiSignIn($instructor);
        $competition = factory(Exam::class)->create([
            'type' => ExamTypes::INSTRUCTOR_COMPETITION,
            'creator_id' => $instructor->id
        ]);

        $response = $this->getJson('api/v1/en/instructor/instructor-competitions/start-competition/'.$competition->id);
        if ($competition->is_started == 1) {
            $response->assertJsonFragment(['detail' => trans('api.The competition is already started')]);
        }else {
            $response->assertJsonFragment(['message' => trans('api.The competition started successfully')]);
        }
    }

    public function test_finish_instructor_competition()
    {
        dump('test_finish_instructor_competition');
        $instructor = $this->authInstructor();

        $this->apiSignIn($instructor);
        $competition = factory(Exam::class)->create([
            'type' => ExamTypes::INSTRUCTOR_COMPETITION,
            'creator_id' => $instructor->id
        ]);

        $response = $this->getJson('api/v1/en/instructor/instructor-competitions/finish-competition/'.$competition->id);
        if ($competition->is_finished == 1) {
            $response->assertJsonFragment(['detail' => trans('api.The competition is already finished')]);
        }else {
            $response->assertJsonFragment(['message' => trans('api.The competition finished successfully')]);
        }
    }
}
