<?php

namespace Database\Seeders;

use App\OurEdu\LearningResources\Enums\DifficultlyLevelEnums;
use App\OurEdu\Users\User;
use App\OurEdu\Options\Option;
use App\OurEdu\Users\UserEnums;
use Illuminate\Database\Seeder;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\Models\Instructor;
use App\OurEdu\Courses\Enums\CourseEnums;
use App\OurEdu\GarbageMedia\GarbageMedia;
use App\OurEdu\LearningResources\Resource;
use App\OurEdu\Users\Models\ContentAuthor;
use App\OurEdu\Subjects\Models\SubModels\Task;
use App\OurEdu\Courses\Models\SubModels\CourseSession;
use App\OurEdu\Subjects\Models\SubModels\SubjectMedia;
use App\OurEdu\ResourceSubjectFormats\Models\Pdf\PdfData;
use App\OurEdu\ResourceSubjectFormats\Models\Page\PageData;
use App\OurEdu\ResourceSubjectFormats\Models\Audio\AudioData;
use App\OurEdu\ResourceSubjectFormats\Models\Flash\FlashData;
use App\OurEdu\ResourceSubjectFormats\Models\Video\VideoData;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use App\OurEdu\Courses\Models\SubModels\CourseStudent;
use App\OurEdu\ResourceSubjectFormats\Models\HotSpot\HotSpotData;
use App\OurEdu\ResourceSubjectFormats\Models\Picture\PictureData;
use App\OurEdu\ResourceSubjectFormats\Models\Complete\CompleteData;
use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropData;
use App\OurEdu\ResourceSubjectFormats\Models\HotSpot\HotSpotAnswer;
use App\OurEdu\ResourceSubjectFormats\Models\Matching\MatchingData;
use App\OurEdu\ResourceSubjectFormats\Models\Complete\CompleteAnswer;
use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropOption;
use App\OurEdu\ResourceSubjectFormats\Models\HotSpot\HotSpotQuestion;
use App\OurEdu\ResourceSubjectFormats\Models\Matching\MatchingOption;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseData;
use App\OurEdu\ResourceSubjectFormats\Models\Complete\CompleteQuestion;
use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropQuestion;
use App\OurEdu\ResourceSubjectFormats\Models\Matching\MatchingQuestion;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseOption;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseQuestion;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use App\OurEdu\ResourceSubjectFormats\Models\Complete\CompleteAcceptedAnswer;
use App\OurEdu\ResourceSubjectFormats\Models\MultiMatching\MultiMatchingData;
use App\OurEdu\ResourceSubjectFormats\Models\MultiMatching\MultiMatchingOption;
use App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceData;
use App\OurEdu\ResourceSubjectFormats\Models\MultiMatching\MultiMatchingQuestion;
use App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceOption;
use App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceQuestion;
use App\OurEdu\Options\Enums\ResourceOptionsSlugEnum;

class CreateDummySubjectSeeder extends Seeder
{
    protected $resourceTypes = [
        'multiple_choice',
        'true_false',
        'video',
        'audio',
        'flash',
        'drag_drop',
        'pdf',
        'picture',
        'matching',
        'multiple_matching',
        'page',
        'complete',
        'hotspot',
    ];

    protected $fillMethods = [
        'multiple_choice' => 'fillMultipleChoice',
        'true_false' => 'fillTrueFalse',
        'video' => 'fillVideo',
        'audio' => 'fillAudio',
        'flash' => 'fillFlash',
        'drag_drop' => 'fillDragDrop',
        'pdf' => 'fillPdf',
        'picture' => 'fillPicture',
        'matching' => 'fillMatching',
        'multiple_matching' => 'fillMultiMatching',
        'page' => 'fillPage',
        'complete' => 'fillComplete',
        'hotspot' => 'fillHotspot',
    ];

    protected $subjectResources = [];
    protected $subjectFormats = [];
    protected $resourceFormats = [];
    protected $absoluteParentSections = [];
    protected $absoluteParentSectionsResources = [];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $studentHeba = User::where('email', 'heba@student.com')->first();

        if ($studentHeba) {
            $studentHeba = $studentHeba->student;
            $dataArr = [
                'educational_system_id' => $studentHeba->educational_system_id,
                'country_id' => $studentHeba->user->country_id,
                'grade_class_id' => $studentHeba->class_id,
                'academical_years_id' => $studentHeba->academical_year_id,
            ];
            $this->subject = Subject::factory()->create($dataArr);
        } else {
            $this->subject = Subject::factory()->create();
        }

        $smeEgypt = User::where('email', 'Egypt@sme.com')->first();
        if ($smeEgypt) {
            $this->sme = $smeEgypt;
        } else {
            $this->sme = User::factory()->create([
                'type' => UserEnums::SME_TYPE
            ]);
        }

        $this->subject->update(['sme_id' => $this->sme->id]);

        $contentAuthor = ContentAuthor::factory()->create();

        $this->contentAuthor = $contentAuthor->user;

        $this->subject->contentAuthors()->sync($this->contentAuthor);

        $instructor = Instructor::factory()->create();

        $this->instructor = $instructor->user;

        $this->subject->instructors()->sync($this->instructor);

        $this->generateResources();
        $this->fillResources();
        $this->randomizeQuestionDifficulty();
        $this->generateSubjectCourses();
        $this->genratePublicCourses();
        $this->createRandomStudents();
        $this->markTasksAsFinished();
        $this->generateMedia();
        $this->specificDataEntering();
        $this->dumpImportantData();
    }


    protected function generateResources()
    {
        collect($this->resourceTypes)->each(function ($resource) {
            $subjectResource = Resource::where('slug', $resource)->firstOrFail();

            // creating parent section
            $absoluteParentSection = SubjectFormatSubject::factory()->create([
                'subject_id' => $this->subject->id,
                'title' =>  'parent section for ' . $resource,
            ]);

            // creating section for the resource
            $subjectFormat = SubjectFormatSubject::factory()->create([
                'subject_id' => $this->subject->id,
                'title' =>  $resource,
                'parent_subject_format_id' => $absoluteParentSection->id
            ]);

            $subjectFormatResoure = ResourceSubjectFormatSubject::factory()->{$resource}()->create([
                'resource_id'   =>  $subjectResource->id,
                'resource_slug' =>  $resource,
                'subject_format_subject_id' =>  $subjectFormat->id,
            ]);

            $absoluteParentSectionResource = ResourceSubjectFormatSubject::factory()->{$resource}()->create([
                'resource_id'   =>  $subjectResource->id,
                'resource_slug' =>  $resource,
                'subject_format_subject_id' =>  $absoluteParentSection->id,
            ]);

            $this->subjectResources[$resource] = $subjectResource->id;
            $this->subjectFormats[$resource] = $subjectFormat->id;
            $this->resourceFormats[$resource] = $subjectFormatResoure->id;
            $this->absoluteParentSections[$resource] = $absoluteParentSection->id;
            $this->absoluteParentSectionsResources[$resource] = $absoluteParentSectionResource->id;

            // pulling tasks
            $this->contentAuthor->contentAuthor->tasks()->create([
                'title' =>  title_case($resource),
                'subject_id' => $this->subject->id,
                'resource_subject_format_subject_id' => $subjectFormatResoure->id,
                'subject_format_subject_id' => $subjectFormat->id,
                'is_assigned' => 1,
            ]);
            $this->contentAuthor->contentAuthor->tasks()->create([
                'title' =>  title_case($resource),
                'subject_id' => $this->subject->id,
                'resource_subject_format_subject_id' => $absoluteParentSectionResource->id,
                'subject_format_subject_id' => $subjectFormat->id,
                'is_assigned' => 1,
            ]);
        });
    }

    protected function fillResources()
    {
        $resourceFormats = ResourceSubjectFormatSubject::with('resource')->find(array_values($this->resourceFormats));
        $parentResourceFormats = ResourceSubjectFormatSubject::with('resource')
            ->find(array_values($this->absoluteParentSectionsResources));

        $parentResourceFormats->each(function ($resourceFormat) {
            if (method_exists($this, $method = $this->fillMethods[$resourceFormat->resource->slug])) {
                $this->$method($resourceFormat);
            };
        });

        $resourceFormats->each(function ($resourceFormat) {
            if (method_exists($this, $method = $this->fillMethods[$resourceFormat->resource->slug])) {
                $this->$method($resourceFormat);
            };
        });
    }

    /*make the difficulty level consistent across all questions*/
    protected function randomizeQuestionDifficulty()
    {
        $this->subject->preparedQuestions()->chunk(5, function ($preparedQuestions) {
            $preparedQuestions->each->update([
                'difficulty_level' => Option::where(
                    'slug',
                    DifficultlyLevelEnums::EASY
                )->first()->slug
            ]);
        });

        dump('difficulty level randomized');
    }


    protected function fillMultipleChoice($resourceFormat)
    {
        // create multi choice data
        $multiChoiceData = MultipleChoiceData::factory()->create([
            'resource_subject_format_subject_id' => $resourceFormat->id
        ]);

        // create questions based on data
        $multiChoiceQuestions = MultipleChoiceQuestion::factory()->count(random_int(10, 20))->create([
            'res_multiple_choice_data_id' => $multiChoiceData->id
        ]);

        $multiChoiceQuestions->each(function ($multiChoiceQuestion) {
            // create options based on questions
            MultipleChoiceOption::factory()->count(random_int(3, 5))->create([
                'res_multiple_choice_question_id' => $multiChoiceQuestion->id
            ]);
            $fakePicture = GarbageMedia::factory()->create();
            moveGarbageMedia($fakePicture->id, $multiChoiceQuestion->media(), 'subject/multiple_choice');
        });

        dump('filled multi choice resource');
    }

    protected function fillTrueFalse($resourceFormat)
    {
        // create data based on format resource
        $trueFalseData = TrueFalseData::factory()->create([
            'resource_subject_format_subject_id' => $resourceFormat->id
        ]);

        // create true false questsions related to data
        $trueFalseQuestions = TrueFalseQuestion::factory()->count(random_int(10, 20))->create([
            'res_true_false_data_id' => $trueFalseData->id
        ]);


        // create true false options related to questions
        $trueFalseQuestions->each(function ($trueFalseQuestion) {
            // if the question is true_false_with_correct
            // create options based on questions
            if (
                $trueFalseQuestion->parentData->TrueFalseType->slug ==
                Option::where('slug', ResourceOptionsSlugEnum::TRUE_FALSE_WITH_CORRECT)->first()->slug
            ) {
                TrueFalseOption::factory()->count(2)->create([
                    'res_true_false_question_id' => $trueFalseQuestion->id
                ]);
            }
            $fakePicture = GarbageMedia::factory()->create();
            moveGarbageMedia($fakePicture->id, $trueFalseQuestion->media(), 'subject/true_false');
        });

        dump('filled true false resource');
    }

    protected function fillDragDrop($resourceFormat)
    {
        $dragDropDatas = DragDropData::factory()->count(random_int(10, 20))->create([
            'resource_subject_format_subject_id' => $resourceFormat->id
        ]);
        foreach ($dragDropDatas as $dragDropData) {
            $dragDropOptions = DragDropOption::factory()->count(3)->create([
                'res_drag_drop_data_id' => $dragDropData->id
            ]);
            foreach ($dragDropOptions as $dragDropOption) {
                $dragDropQuestion =  DragDropQuestion::factory()->create([
                    'res_drag_drop_data_id' => $dragDropData->id,
                    'correct_option_id' => $dragDropOption->id
                ]);
                $fakePicture = GarbageMedia::factory()->create();
                moveGarbageMedia($fakePicture->id, $dragDropQuestion->media(), 'subject/drag_drop');
            }

            //create additional option to be alone :D

            $dragDropOption = DragDropOption::factory()->create([
                'res_drag_drop_data_id' => $dragDropData->id
            ]);
        }

        dump('filled drag & drop resource');
    }

    protected function fillMatching($resourceFormat)
    {
        $matchingDatas = MatchingData::factory()->count(random_int(10, 20))->create([
            'resource_subject_format_subject_id' => $resourceFormat->id
        ]);

        foreach ($matchingDatas as $matchingData) {
            $matchingQuestions = MatchingQuestion::factory()->count(3)->create([
                'res_matching_data_id' => $matchingData->id
            ]);

            foreach ($matchingQuestions as $matchingQuestion) {
                MatchingOption::factory()->create([
                    'res_matching_data_id' => $matchingData->id,
                    'res_matching_question_id' => $matchingQuestion->id
                ]);
                $fakePicture = GarbageMedia::factory()->create();
                moveGarbageMedia($fakePicture->id, $matchingQuestion->media(), 'subject/matching');
            }
        }

        dump('filled matching resource');
    }

    protected function fillMultiMatching($resourceFormat)
    {
        $multiMatchingDatas = MultiMatchingData::factory()->count(random_int(10, 20))->create([
            'resource_subject_format_subject_id' => $resourceFormat->id
        ]);

        foreach ($multiMatchingDatas as $multiMatchingData) {
            $multiMatchingQuestions = MultiMatchingQuestion::factory()->count(3)->create([
                'res_multi_matching_data_id' => $multiMatchingData->id
            ]);

            $multiMatchingOptions = MultiMatchingOption::factory()->count(6)->create([
                'res_multi_matching_data_id' => $multiMatchingData->id
            ]);

            $optionsIds = $multiMatchingOptions->pluck('id')->toArray();
            $optionsOffest = 0;

            foreach ($multiMatchingQuestions as $multiMatchingQuestion) {
                $multiMatchingQuestion->options()->sync([$optionsIds[$optionsOffest], $optionsIds[$optionsOffest + 1]]);

                $fakePicture = GarbageMedia::factory()->create();
                moveGarbageMedia($fakePicture->id, $multiMatchingQuestion->media(), 'subject/multi_matching');

                $optionsOffest += 2;
            }
        }

        dump('filled multi matching resource');
    }

    protected function fillComplete($resourceFormat)
    {
        // create data based on format resource
        $completeData = CompleteData::factory()->create([
            'resource_subject_format_subject_id' => $resourceFormat->id
        ]);

        // create complete questsions related to data
        $completeQuestions = CompleteQuestion::factory()->count(random_int(10, 20))->create([
            'res_complete_data_id' => $completeData->id
        ]);


        // create complete answers related to questions
        $completeQuestions->each(function ($completeQuestion) {
            // create answers based on questions
            CompleteAnswer::factory()->create([
                'res_complete_question_id' => $completeQuestion->id
            ]);

            CompleteAcceptedAnswer::factory()->count(2)->create([
                'res_complete_question_id' => $completeQuestion->id
            ]);
        });

        dump('fill complete resource');
    }

    protected function fillPicture($resourceFormat)
    {
        // create data based on format resource
        $pictureData = PictureData::factory()->create([
            'resource_subject_format_subject_id' => $resourceFormat->id
        ]);

        $fakePictures = GarbageMedia::factory()->count(2)->create()->pluck('id')->toArray();

        moveGarbageMedia($fakePictures, $pictureData->media(), 'subject/pictures');

        dump('fill picture resource');
    }

    protected function fillPdf($resourceFormat)
    {
        // create data based on format resource
        $pdfData = PdfData::factory()->create([
            'resource_subject_format_subject_id' => $resourceFormat->id
        ]);

        $fakePdf = GarbageMedia::factory()->count(2)->pdf()->create()->pluck('id')->toArray();

        moveGarbageMedia($fakePdf, $pdfData->media(), 'subject/pdfs');

        dump('fill pdf resource');
    }


    protected function fillVideo($resourceFormat)
    {
        // create data based on format resource
        $videoData = VideoData::factory()->create([
            'resource_subject_format_subject_id' => $resourceFormat->id
        ]);

        $fakeFiles = GarbageMedia::factory()->count(2)->video()->create()->pluck('id')->toArray();

        moveGarbageMedia($fakeFiles, $videoData->media(), 'subject/videos');

        dump('fill video resource');
    }

    protected function fillAudio($resourceFormat)
    {
        // create data based on format resource
        $audioData = AudioData::factory()->create([
            'resource_subject_format_subject_id' => $resourceFormat->id
        ]);

        $fakeFiles = GarbageMedia::factory()->count(2)->audio()->create()->pluck('id')->toArray();

        moveGarbageMedia($fakeFiles, $audioData->media(), 'subject/audios');

        dump('fill audio resource');
    }

    protected function fillFlash($resourceFormat)
    {
        // create data based on format resource
        $flashData = FlashData::factory()->create([
            'resource_subject_format_subject_id' => $resourceFormat->id
        ]);

        $fakeFiles = GarbageMedia::factory()->count(2)->flash()->create()->pluck('id')->toArray();

        moveGarbageMedia($fakeFiles, $flashData->media(), 'subject/flashes');

        dump('fill flash resource');
    }

    protected function fillPage($resourceFormat)
    {
        // create data based on format resource
        $pageData = PageData::create([
            'resource_subject_format_subject_id' => $resourceFormat->id,
            'page'  =>  'some content is here',
            'title' => 'title here'
        ]);

        dump('fill page resource');
    }

    protected function fillHotspot($resourceFormat)
    {
        // create data based on format resource
        $hotspotData = HotSpotData::factory()->create([
            'resource_subject_format_subject_id' => $resourceFormat->id
        ]);

        // create hotspot questsions related to data
        $hotSpotQuestions = HotSpotQuestion::factory()->count(random_int(10, 20))->create([
            'res_hot_spot_data_id' => $hotspotData->id
        ]);


        // create hotspot answers related to questions
        $hotSpotQuestions->each(function ($question) {
            // create answers based on questions

            HotSpotAnswer::factory()->count(random_int(2, 4))->create([
                'res_hot_spot_question_id' => $question->id
            ]);
            $fakePicture = GarbageMedia::factory()->create();
            moveGarbageMedia($fakePicture->id, $question->media(), 'subject/hot_spot');
        });

        dump('fill hotspot resource');
    }

    protected function generateSubjectCourses()
    {
        $courses = Course::factory()->count(2)->create([
            'type'  =>  CourseEnums::SUBJECT_COURSE,
            'subject_id'    =>  $this->subject->id,
        ]);

        $courses->each(function ($course) {
            CourseSession::factory()->count(random_int(2, 5))->create(['course_id' => $course->id]);
        });

        dump('subject courses generated');

        $this->subjectCourses = $courses->pluck('id')->toArray();
        $this->subjectCoursesObjects = $courses;
    }

    protected function genratePublicCourses()
    {
        $courses = Course::factory()->count(2)->create([
            'type'  =>  CourseEnums::PUBLIC_COURSE,
            'subject_id'    =>  null
        ]);

        $courses->each(function ($course) {
            CourseSession::factory()->count(random_int(2, 5))->create(['course_id' => $course->id]);
        });

        dump('public courses generated');

        $this->publicCourses = $courses->pluck('id')->toArray();
        $this->publicCoursesObjects = $courses;
    }

    protected function createRandomStudents()
    {
        $students = Student::factory()->count(2)->create();

        $students->each(function ($student) {
            foreach ($this->publicCoursesObjects as $publicCoursesObject) {
                CourseStudent::create([
                    'course_id' => $publicCoursesObject->id,
                    'student_id' => $student->id,
                    'instructor_id' =>  $publicCoursesObject->instructor_id,
                ]);
            }

            foreach ($this->subjectCoursesObjects as $subjectCoursesObject) {
                CourseStudent::create([
                    'course_id' => $subjectCoursesObject->id,
                    'student_id' => $student->id,
                    'instructor_id' =>  $subjectCoursesObject->instructor_id,
                ]);
            }

            $student->subjects()->sync($this->subject);
        });

        $parents = User::factory()->count(2)->create(['type' => UserEnums::PARENT_TYPE]);

        $parents->each(function ($parent) use ($students) {
            $parent->students()->sync($students->pluck('id')->toArray());
        });

        $this->students = $students->pluck('user.email')->toArray();
        $this->parents = $parents->pluck('email')->toArray();
    }

    protected function markTasksAsFinished()
    {
        Task::where('subject_id', $this->subject->id)->update(['is_active' => false]);

        dump('Subject tasks marked as finished');
    }

    protected function generateMedia()
    {
        SubjectMedia::factory()->count(random_int(30, 45))->create([
            'subject_id'    =>  $this->subject->id
        ]);

        dump('Subject media generated');
    }

    protected function specificDataEntering()
    {
        $studentHeba = User::where('email', 'heba@student.com')->first();
        if ($studentHeba) {
            $studentHeba->student->subscribe()->create([
                'subject_id' => $this->subject->id
            ]);
            array_push($this->students, $studentHeba->email);
        }
    }
    protected function dumpImportantData()
    {
        dump([
            'subject'   =>  $this->subject->id,
            'subject_courses'   =>  $this->subjectCourses,
            'public_courses'   =>  $this->publicCourses,
            'parent_subject_format_subjects' =>  $this->absoluteParentSections,
            'parent_resource_subject_format' =>  $this->absoluteParentSectionsResources,
            'subject_format_subjects' =>  $this->subjectFormats,
            'resource_subject_format' =>  $this->resourceFormats,
            'content_author_email' =>  $this->contentAuthor->email,
            'instructor_email' =>  $this->instructor->email,
            'sme_email' =>  $this->sme->email,
            'students'  =>  $this->students,
            'parents'  =>  $this->parents
        ]);
    }
}
