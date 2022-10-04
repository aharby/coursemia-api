<?php


namespace Tests\Feature\Exams\Student\Controller\Api;

use ExamsSeeder;
use Tests\TestCase;
use App\OurEdu\Users\User;
use App\OurEdu\Options\Option;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Exams\Enums\ExamTypes;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Exams\Models\ExamQuestion;
use App\OurEdu\LearningResources\Resource;
use App\OurEdu\Options\Enums\OptionsTypes;
use Illuminate\Foundation\Testing\WithFaker;
use App\OurEdu\Subjects\Models\SubModels\Task;
use App\OurEdu\Exams\Models\PrepareExamQuestion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\OurEdu\Subjects\Models\SubModels\ContentAuthorTask;
use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseData;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseOption;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseQuestion;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;

class ExamApiControllerTest extends TestCase
{
    use WithFaker;

    public function test_start_exam()
    {
        $this->disableExceptionHandling();

        dump('test_start_exam');
        $this->seed('OptionsSeeder');
        $exam = create(Exam::class, ['is_started' => 0, 'is_finished' => 0, 'type' => ExamTypes::EXAM]);

        create(ExamQuestion::class, ['exam_id' => $exam->id]);

        $user = $exam->student->user;
        $this->apiSignIn($user);

        $response = $this->postJson('/api/v1/en/student/exams/start-exam/'.$exam->id, [])->assertOk();

        $this->assertDatabaseHas('exams', [
            "is_started" => 1]);
    }

    public function test_finish_exam()
    {
        $this->disableExceptionHandling();

        dump('test_finish_exam');

        $exam = create(Exam::class, ['is_started' => 1, 'is_finished' => 0, 'type' => ExamTypes::EXAM, 'result' => null]);

        create(ExamQuestion::class, ['exam_id' => $exam->id, 'is_correct_answer' => $this->faker->boolean()], 5);

        $user = $exam->student->user;
        $this->apiSignIn($user);

        $response =$this->postJson('/api/v1/en/student/exams/finish-exam/'.$exam['id'], [])->assertOk();
        $this->assertDatabaseHas('exams', [
            "is_finished" => 1]);
    }

    public function test_generate_exam()
    {
        dump('test_generate_exam');

        $subject = factory(Subject::class)->create();
        $student = create(Student::class);
        $user = $student->user;

        $this->apiSignIn($user);

        $option = Option::where('type', OptionsTypes::RESOURCE_DIFFICULTY_LEVEL)->first();

        $student->subjects()->sync($subject);

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
        $response = $this->postJson('api/v1/en/student/exams/generate-exam', $data)
            ->assertJsonFragment(['message' => trans('api.Exam generated')]);

        $this->assertDatabaseHas('exams', [
            'subject_id'    =>  $subject->id,
            'student_id'    =>  $student->id,
           'difficulty_level' => $option->slug
       ]);

        $this->assertDatabaseHas('exam_questions', [
            'subject_id' => $subject->id
        ]);
    }

    public function test_get_next_or_back_question()
    {
        dump('test_get_next_or_back_question');

        $this->disableExceptionHandling();

        $exam = create(Exam::class, ['is_started' => 1, 'is_finished' => 0, 'type' => ExamTypes::EXAM]);

        create(ExamQuestion::class, ['exam_id' => $exam->id], 2);

        $user = $exam->student->user;
        $this->apiSignIn($user);

        $response = $this->getJson(route('exams.get.next-back-questions', ['examId' => $exam->id, 'language' => 'en', 'current_question' => 1]));

        $response->assertOk();
        $responseArr = $response->decodeResponseJson();
        $nextQuestionUrl = $responseArr['links']['next'];
        $response = $this->getJson($nextQuestionUrl);
        $response->assertOk();
    }

    public function test_generate_exam_from_previous_exam()
    {
        dump('test_generate_exam_from_previous_exam');

        $student = $this->authStudent();
        $this->apiSignIn($student);
        $previousExam = factory(Exam::class)->create([
            'questions_number' => 5,
            'student_id' =>$student->student->id,
            'difficulty_level' => Option::where('type', OptionsTypes::RESOURCE_DIFFICULTY_LEVEL)->first()->id
        ]);

        $this->postJson('api/v1/en/student/exams/generate-exam-from-previous-exam/'. $previousExam->id);
        $this->assertDatabaseHas('exams', [
            'student_id' => $student->student->id,
            'questions_number' => $previousExam->questions_number
        ]);
    }

    public function test_list_exams()
    {
        dump('test_list_exams');

        $student = $this->authStudent();
        $this->apiSignIn($student);

        $exam = factory(Exam::class)->create([
            'questions_number' => 5,
            'student_id' =>$student->student->id,
        ]);

        $this->getJson('api/v1/en/student/exams/list-exams')->assertOk();
    }

    public function test_generate_practice()
    {
        dump('test_generate_practice');

        $subject = factory(Subject::class)->create();
        $student = create(Student::class);
        $user = $student->user;

        $this->apiSignIn($user);

        $option = Option::where('type', OptionsTypes::RESOURCE_DIFFICULTY_LEVEL)->first();

        $student->subjects()->sync($subject);

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
                    'subject_id' => $subject->id,
                    'subject_format_subject_ids' => $subject->subjectFormatSubject()->pluck('id')->toArray()
                ]
            ]
        ];

        $response = $this->postJson('api/v1/en/student/practices/generate-practice', $data)
            ->assertJsonFragment(['message' => trans('api.Practice generated')]);

        $this->assertDatabaseHas('exams', [
            'subject_id'    =>  $subject->id,
            'student_id'    =>  $student->id,
       ]);

        $this->assertDatabaseHas('exam_questions', [
            'subject_id' => $subject->id
        ]);
    }
}
