<?php

namespace Database\Seeders;

use App\OurEdu\Users\User;
use App\OurEdu\Options\Option;
use Illuminate\Database\Seeder;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\GarbageMedia\GarbageMedia;
use App\OurEdu\LearningResources\Resource;
use App\OurEdu\Subjects\Models\SubModels\Task;
use App\OurEdu\Subjects\Models\SubModels\SubjectMedia;
use App\OurEdu\ResourceSubjectFormats\Models\Pdf\PdfData;
use App\OurEdu\ResourceSubjectFormats\Models\Page\PageData;
use App\OurEdu\ResourceSubjectFormats\Models\Audio\AudioData;
use App\OurEdu\ResourceSubjectFormats\Models\Flash\FlashData;
use App\OurEdu\ResourceSubjectFormats\Models\Video\VideoData;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
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

class AptitudeSeeder extends Seeder
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
    protected $quantitativeSubjectFormats = [];
    protected $speechSubjectFormats = [];
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
        // Student
        $studentHeba = User::where('email', 'heba@student.com')->first()?->student;
        $this->subject = Subject::create([
            'name' => 'Aptitude',
            'is_active' => true,
            'is_aptitude' => Subject::whereIsAptitude(1)->first() ? false : true,
            'color' => '#80ff00',
        ]);

        // SME
        $this->sme = User::where('email', 'Egypt@sme.com')->first();

        $this->subject->update(['sme_id' => $this->sme?->id]);

        // Content Author
        $this->contentAuthor = User::where('email', 'Egypt@author.com')->first();

        $this->subject->contentAuthors()->sync($this->contentAuthor);

        // Instructor
        $this->instructor = User::where('email', 'Egypt@instructor.com')->first();

        $this->subject->instructors()->sync($this->instructor);

        $this->setAbsoluteParentSections();
        $this->setQuantitativeSubSections();
        $this->setSpeechSubSections();

        // create and fill resource for quantitive subsections
        collect($this->quantitativeSubjectFormats)->each(function ($subjectFormatId, $subjectFormat) {
            $this->generateResources($subjectFormatId, $subjectFormat);
        });

        // create and fill resource for Speech subsections
        collect($this->speechSubjectFormats)->each(function ($subjectFormatId, $subjectFormat) {
            $this->generateResources($subjectFormatId, $subjectFormat);
        });

        $this->fillResources();
        $this->markTasksAsFinished();
        $this->generateMedia();
        $this->specificDataEntering($studentHeba);
        $this->dumpImportantData();
    }

    public function setAbsoluteParentSections()
    {
        // quantitativeSection
        $quantitativeSection = SubjectFormatSubject::create([
            'title' => 'quantitative',
            'description' => 'Aptitude Section',
            'is_active' => true,
            'is_editable' => false,
            'slug' => 'quantitative',
            'subject_id' => $this->subject->id,
        ]);

        // speechSection
        $speechSection = SubjectFormatSubject::create([
            'title' => 'Verbal',
            'description' => 'Aptitude Section',
            'is_active' => true,
            'is_editable' => false,
            'slug' => 'verbal',
            'subject_id' => $this->subject->id,
        ]);

        $this->absoluteParentSections = [
            'quantitativeSection' => $quantitativeSection->id,
            'speechSection' => $speechSection->id,
        ];

        dump('Absolute parentSections are created');
    }

    public function setQuantitativeSubSections()
    {
        $math = SubjectFormatSubject::create([
            'title' => 'Math',
            'description' => 'Aptitude quantitative Section',
            'subject_id' => $this->subject->id,
            'parent_subject_format_id' => $this->absoluteParentSections['quantitativeSection'],
            'is_active' => true,
            'slug' => 'math',
            'is_editable' => false,
        ]);

        $geometry = SubjectFormatSubject::create([
            'title' => 'Geometry',
            'description' => 'Aptitude quantitative Section',
            'subject_id' => $this->subject->id,
            'parent_subject_format_id' => $this->absoluteParentSections['quantitativeSection'],
            'is_active' => true,
            'slug' => 'geometry',
            'is_editable' => false,
        ]);

        $algebra = SubjectFormatSubject::create([
            'title' => 'algebra',
            'description' => 'Aptitude quantitative Section',
            'parent_subject_format_id' => $this->absoluteParentSections['quantitativeSection'],
            'is_active' => true,
            'subject_id' => $this->subject->id,
            'slug' => 'algebra',
            'is_editable' => false,
        ]);

        $statistics = SubjectFormatSubject::create([
            'title' => 'statistics',
            'description' => 'Aptitude quantitative Section',
            'parent_subject_format_id' => $this->absoluteParentSections['quantitativeSection'],
            'subject_id' => $this->subject->id,
            'is_active' => true,
            'slug' => 'statistics',
            'is_editable' => false,
        ]);

        $comparison = SubjectFormatSubject::create([
            'title' => 'comparison',
            'description' => 'Aptitude quantitative Section',
            'parent_subject_format_id' => $this->absoluteParentSections['quantitativeSection'],
            'subject_id' => $this->subject->id,
            'is_active' => true,
            'slug' => 'comparison',
            'is_editable' => false,
        ]);

        $this->quantitativeSubjectFormats = [
            'math' => $math->id,
            'geometry' => $geometry->id,
            'algebra' => $algebra->id,
            'statistics' => $statistics->id,
            'comparison' => $comparison->id,
        ];

        dump('Quantitative subsections are created');
    }

    public function setSpeechSubSections()
    {
        $readComprehension = SubjectFormatSubject::create([
            'title' => 'read comprehension',
            'description' => 'Aptitude Verbal Section',
            'subject_id' => $this->subject->id,
            'parent_subject_format_id' => $this->absoluteParentSections['speechSection'],
            'is_active' => true,
            'slug' => 'read_comprehension',
            'is_editable' => false,
        ]);

        $completeSentences = SubjectFormatSubject::create([
            'title' => 'complete sentences',
            'description' => 'Aptitude Verbal Section',
            'subject_id' => $this->subject->id,
            'parent_subject_format_id' => $this->absoluteParentSections['speechSection'],
            'is_active' => true,
            'slug' => 'complete_sentences',
            'is_editable' => false,
        ]);

        $verbalSymmetry = SubjectFormatSubject::create([
            'title' => 'verbal symmetry',
            'description' => 'Aptitude Verbal Section',
            'subject_id' => $this->subject->id,
            'parent_subject_format_id' => $this->absoluteParentSections['speechSection'],
            'is_active' => true,
            'slug' => 'verbal_symmetry',
            'is_editable' => false,
        ]);

        $remainingError = SubjectFormatSubject::create([
            'title' => 'remaining error',
            'description' => 'Aptitude Verbal Section',
            'subject_id' => $this->subject->id,
            'parent_subject_format_id' => $this->absoluteParentSections['speechSection'],
            'is_active' => true,
            'slug' => 'remaining_error',
            'is_editable' => false,
        ]);

        $correlation = SubjectFormatSubject::create([
            'title' => 'correlation and variation',
            'description' => 'Aptitude Verbal Section',
            'subject_id' => $this->subject->id,
            'parent_subject_format_id' => $this->absoluteParentSections['speechSection'],
            'is_active' => true,
            'slug' => 'correlation_and_variation',
            'is_editable' => false,
        ]);

        $this->speechSubjectFormats = [
            'readComprehension' => $readComprehension->id,
            'completeSentences' => $completeSentences->id,
            'verbalSymmetry' => $verbalSymmetry->id,
            'remainingError' => $remainingError->id,
            'correlation' => $correlation->id,
        ];

        dump('Speech subsections are created');
    }

    protected function generateResources($subjectFormatId, $subjectFormat)
    {
        collect($this->resourceTypes)->each(function ($resource) use ($subjectFormatId, $subjectFormat) {
            $subjectResource = Resource::where('slug', $resource)->firstOrFail();

            // creating resource for sub sections
            $subjectFormatResoure = ResourceSubjectFormatSubject::factory()->{$resource}()->create([
                'resource_id' => $subjectResource->id,
                'resource_slug' => $resource,
                'subject_format_subject_id' => $subjectFormatId,
            ]);

            // appending to arrays
            $this->subjectResources[$resource] = $subjectResource->id;
            $this->resourceFormats[$subjectFormat . '_' . $resource] = $subjectFormatResoure->id;

            // creating pulling tasks
            $this->contentAuthor?->contentAuthor->tasks()->create([
                'title' => title_case($resource),
                'subject_id' => $this->subject->id,
                'resource_subject_format_subject_id' => $subjectFormatResoure->id,
                'subject_format_subject_id' => $subjectFormatId,
                'is_assigned' => 1,
            ]);
        });
    }

    protected function fillResources()
    {
        // get subsection resources
        $resourceFormats = ResourceSubjectFormatSubject::with('resource')->find(array_values($this->resourceFormats));

        // Fill each of them with the corresponding filling method
        $resourceFormats->each(function ($resourceFormat) {
            if (method_exists($this, $method = $this->fillMethods[$resourceFormat->resource->slug])) {
                $this->$method($resourceFormat);
            };
        });
    }

    protected function fillMultipleChoice($resourceFormat)
    {
        // create multi choice data
        $multiChoiceData = MultipleChoiceData::factory()->create([
            'resource_subject_format_subject_id' => $resourceFormat->id
        ]);

        // create questions based on data
        $multiChoiceQuestions = MultipleChoiceQuestion::factory()->count(random_int(50, 70))->create([
            'res_multiple_choice_data_id' => $multiChoiceData->id
        ]);

        $multiChoiceQuestions->each(function ($multiChoiceQuestion) {
            // create options based on questions
            MultipleChoiceOption::factory()->count(random_int(3, 5))
                ->create([
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
        $trueFalseQuestions = TrueFalseQuestion::factory()->count(random_int(50, 70))
            ->create([
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

                TrueFalseOption::factory()->count(2)
                    ->create([
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
        $dragDropDatas = DragDropData::factory()->count(random_int(50, 70))
            ->create([
                'resource_subject_format_subject_id' => $resourceFormat->id
            ]);
        foreach ($dragDropDatas as $dragDropData) {
            $dragDropOptions = DragDropOption::factory()->count(3)
                ->create([
                    'res_drag_drop_data_id' => $dragDropData->id
                ]);
            foreach ($dragDropOptions as $dragDropOption) {
                $dragDropQuestion = DragDropQuestion::factory()->create([
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
        $matchingDatas = MatchingData::factory()->count(random_int(50, 70))->create([
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
        $multiMatchingDatas = MultiMatchingData::factory()->count(random_int(50, 70))->create([
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
        $completeQuestions = CompleteQuestion::factory()->count(random_int(50, 70))->create([
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
            'page' => 'some content is here',
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

        $fakeFiles = GarbageMedia::factory()->count(2)->create()->pluck('id')->toArray();

        moveGarbageMedia($fakeFiles, $hotspotData->media(), 'subject/hotSpots');

        // create hotspot questsions related to data
        $hotSpotQuestions = HotSpotQuestion::factory()->count(random_int(50, 70))->create([
            'res_hot_spot_data_id' => $hotspotData->id
        ]);

        // create hotspot answers related to questions
        $hotSpotQuestions->each(function ($question) {
            // create answers based on questions

            HotSpotAnswer::factory()->count(random_int(2, 4))->create([
                'res_hot_spot_question_id' => $question->id
            ]);
        });

        dump('fill hotspot resource');
    }

    protected function markTasksAsFinished()
    {
        Task::where('subject_id', $this->subject->id)->update(['is_active' => false]);

        dump('Subject tasks marked as finished');
    }

    protected function generateMedia()
    {
        SubjectMedia::factory()->count(random_int(30, 45))->create([
            'subject_id' => $this->subject->id
        ]);

        dump('Subject media generated');
    }

    protected function specificDataEntering($studentHeba)
    {
        $studentHeba->subscribe()->create([
            'subject_id' => $this->subject->id
        ]);
    }

    protected function dumpImportantData()
    {
        dump([
            'subject' => $this->subject->id,
            'parent_subject_format_subjects' => $this->absoluteParentSections,
            'parent_resource_subject_format' => $this->absoluteParentSectionsResources,
            'quantitative_subject_format_subjects' => $this->quantitativeSubjectFormats,
            'speech_subject_format_subjects' => $this->speechSubjectFormats,
            'resource_subject_format' => $this->resourceFormats,
            'content_author_email' => $this->contentAuthor->email,
            'instructor_email' => $this->instructor->email,
            'sme_email' => $this->sme->email,
        ]);
    }
}
