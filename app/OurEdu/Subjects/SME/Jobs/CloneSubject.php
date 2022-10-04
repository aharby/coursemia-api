<?php

namespace App\OurEdu\Subjects\SME\Jobs;

use App\OurEdu\ResourceSubjectFormats\Models\Complete\CompleteData;
use App\OurEdu\ResourceSubjectFormats\Models\HotSpot\HotSpotData;
use Log;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Queue\SerializesModels;
use App\OurEdu\Subjects\Models\Subject;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\OurEdu\Subjects\Events\SubjectModified;
use App\OurEdu\ResourceSubjectFormats\Models\Pdf\PdfData;
use App\OurEdu\ResourceSubjectFormats\Models\Page\PageData;
use App\OurEdu\ResourceSubjectFormats\Models\Audio\AudioData;
use App\OurEdu\ResourceSubjectFormats\Models\Flash\FlashData;
use App\OurEdu\ResourceSubjectFormats\Models\Video\VideoData;
use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use App\OurEdu\ResourceSubjectFormats\Models\Picture\PictureData;
use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropData;
use App\OurEdu\ResourceSubjectFormats\Models\Matching\MatchingData;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseData;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use App\OurEdu\ResourceSubjectFormats\Models\MultiMatching\MultiMatchingData;
use App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceData;

class CloneSubject implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $subjectData;
    private $subject;
    private $clonedSubject;


    /**
     * Create a new job instance.
     *
     * @param $subject
     * @param $subjectData
     */
    public function __construct(Subject $subject, $subjectData)
    {
        $this->subjectData = $subjectData;
        $this->subject = $subject;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            DB::beginTransaction();
            // clone subject without total points
            $subjectName = $this->subject->name;
            if (!is_null($this->subjectData->name)) {
                $subjectName = $this->subjectData->name;
            }
            $academicYear = $this->subject->academical_years_id;
            if (!is_null($this->subjectData->academic_years)) {
                $academicYear = $this->subjectData->academic_years;
            }
            if (!is_null($this->subjectData->subject_library_text)) {
                $subject_library_text = $this->subjectData->subject_library_text;
            }
            if (is_null($this->subjectData->subject_library_text)) {
                $subject_library_text = "Not Found Library Text!!";
            }
            Log::channel('cloneSubject')->info('Subject Name = '.$subjectName.' subject academic year = '.$academicYear);

            $this->clonedSubject = Subject::create([
                'name' => $subjectName,
                'start_date' => $this->subject->start_date,
                'end_date' => $this->subject->end_date,
                'subscription_cost' => $this->subject->subscription_cost,
                'educational_system_id' => $this->subject->educational_system_id,
                'country_id' => $this->subject->country_id,
                'grade_class_id' => $this->subject->grade_class_id,
                'educational_term_id' => $this->subject->educational_term_id,
                'academical_years_id' => $academicYear,
                'sme_id' => $this->subject->sme_id,
                'created_by' => $this->subject->created_by,
                'is_active' => $this->subject->is_active,
                'section_type' => $this->subject->section_type,
                'subject_library_text' => $subject_library_text,
                'subject_library_attachment' => $this->subject->subject_library_attachment,
            ]);

            // adding subject media library
            $this->subject->media()->each(function ($subjectMedia){
                $this->clonedSubject->media()->create([
                    'source_filename' => $subjectMedia->source_filename,
                    'filename' => $subjectMedia->filename,
                    'size' => $subjectMedia->size,
                    'mime_type' => $subjectMedia->mime_type,
                    'url' => $subjectMedia->url,
                    'extension' => $subjectMedia->extension,
                    'status' =>$subjectMedia->status,
                ]);
            });

            // Log clone Subject
            Log::channel('cloneSubject')->info('Subject Created Successfully with new id '.$this->clonedSubject->id);

            $subjectFormatSubjects = $this->subjectData->subjectFormatSubjects ?? [];

            if (count($subjectFormatSubjects) > 0) {
                foreach ($subjectFormatSubjects as $section) {
                    $parentSubjectFormatSubjectId = $this->createSubjectFormatSubject($section, null);

                    if (isset($section->subjectFormatSubjects) && !is_null($parentSubjectFormatSubjectId)) {
                        $this->createNestedSection($section->subjectFormatSubjects, $parentSubjectFormatSubjectId);
                    }
                }
            }
            // update total points
            if ($this->clonedSubject->subjectFormatSubject()->whereNull('parent_subject_format_id')->exists()) {
                $subjectTotalPoints = $this->clonedSubject->subjectFormatSubject()->whereNull('parent_subject_format_id')->sum('total_points');
                $this->clonedSubject->update(['total_points' => $subjectTotalPoints]);
            }

            SubjectModified::dispatch([], $this->clonedSubject->toArray(), 'Subject cloned');

            DB::commit();
//            Redis::publish('clone_subject_'.$this->subject->id, json_encode([
//                'type' => 'clone_subject',
//                'data' => [
//                    'old_subject_id' => $this->subject->id,
//                    'new_subject_id' => $this->clonedSubject->id
//                ]
//            ]));
        } catch (Throwable $e) {
            DB::rollBack();
            Log::channel('cloneSubject')->info($e->getMessage().' Line'.$e->getLine().' File'.$e->getFile());
        }
    }


    private function createSubjectFormatSubject($subjectFormatSubject, $parentSubjectFormatId)
    {
        // delete subject format subject
        $subjectFormatSubjectOld = $subjectFormatSubject->getId();

        if (isset($subjectFormatSubjectOld) && $subjectFormatSubjectOld > 0) {
            $subjectFormatSubjectOld = SubjectFormatSubject::where('subject_id', $this->subject->id)->where(
                'id',
                $subjectFormatSubjectOld
            )->with('resourceSubjectFormatSubject')->withCount('resourceSubjectFormatSubject')->first();
            if ($subjectFormatSubjectOld) {
                $clonedSubjectFormatSubject = SubjectFormatSubject::create([
                    'title' => $subjectFormatSubjectOld->title,
                    'description' => $subjectFormatSubjectOld->description,
                    'is_active' => $subjectFormatSubjectOld->is_active,
                    'subject_id' => $this->clonedSubject->id,
                    'subject_type' => $this->clonedSubject->subject_type,
                    'parent_subject_format_id' => $parentSubjectFormatId,
                    'created_by' => $subjectFormatSubjectOld->created_by,
                    'is_editable' => 1,
                    'list_order_key' => $subjectFormatSubjectOld->list_order_key,
                ]);
                Log::channel('cloneSubject')
                    ->info('Subject Format Subject Created Successfully with new id '.$clonedSubjectFormatSubject->id.' parent Id >'.$parentSubjectFormatId);
                Log::channel('cloneSubject')->info($subjectFormatSubjectOld);
                if ($subjectFormatSubjectOld->resource_subject_format_subject_count > 0) {
                    $this->createResourceSubjectFormatSubject(
                        $subjectFormatSubjectOld->resourceSubjectFormatSubject,
                        $clonedSubjectFormatSubject
                    );
                }
                return $clonedSubjectFormatSubject->id;
            } else {
                Log::channel('cloneSubject')
                    ->info('Old subject '.$this->subject->id.' new Subject: Subject Id '.$this->clonedSubject->id.'  not find subject format subject '.$subjectFormatSubject->getId());
                return null;
            }
        }
        return null;
    }


    private function createResourceSubjectFormatSubject($resources, $subjectFormatSubject)
    {
        $sumOfTotalPoint = 0;
        foreach ($resources as $resource) {
            $clonedResource = ResourceSubjectFormatSubject::create([
                'accept_criteria' => $resource->accept_criteria,
                'resource_id' => $resource->resource_id,
                'resource_slug' => $resource->resource_slug,
                'subject_format_subject_id' => $subjectFormatSubject->id,
                'is_active' => $resource->is_active,
                'created_by' => $resource->created_by,
                'is_editable' => false,
                'list_order_key' => $resource->list_order_key,
                'total_points' => $resource->total_points,
            ]);
            $sumOfTotalPoint = $sumOfTotalPoint + $clonedResource->total_points;
            $this->createResourcesData($clonedResource, $resource);

            $task = $resource->task;
            if ($task) {
                $clonedTask = $task->replicate();
                $clonedTask->resource_subject_format_subject_id = $clonedResource->id;
                $clonedTask->subject_format_subject_id = $subjectFormatSubject->id;
                $clonedTask->save();
            }
        }
        $subjectFormatSubject->total_points = $sumOfTotalPoint;
        $subjectFormatSubject->save();

        if (!is_null($subjectFormatSubject->parent_subject_format_id)) {
            $this->updateTotalPointOfSubjectFormatSubject(
                $subjectFormatSubject->parent_subject_format_id,
                $sumOfTotalPoint
            );
        }
    }

    private function updateTotalPointOfSubjectFormatSubject($subjectFormatSubjectId, $totalPoint)
    {
        $parentSubject = SubjectFormatSubject::where(
            'id',
            $subjectFormatSubjectId
        )->where(
            'subject_id',
            $this->clonedSubject->id
        )->first();
        if ($parentSubject) {
            $parentSubject->total_points = $parentSubject->total_points + $totalPoint;
            $parentSubject->save();
            if (!is_null($parentSubject->parent_subject_format_id)) {
                $this->updateTotalPointOfSubjectFormatSubject(
                    $parentSubject->parent_subject_format_id,
                    $parentSubject->total_points
                );
            }
        }
    }

    private function createNestedSection($sections, $parentId)
    {
        foreach ($sections as $section) {
            $parentSubjectFormatSubjectId = $this->createSubjectFormatSubject($section, $parentId);
            if (isset($section->subjectFormatSubjects) && !is_null($parentSubjectFormatSubjectId)) {
                $this->createNestedSection($section->subjectFormatSubjects, $parentSubjectFormatSubjectId);
            }
        }
    }

    private function createResourcesData(
        ResourceSubjectFormatSubject $clonedResource,
        ResourceSubjectFormatSubject $oldResource
    ) {
        Log::channel('cloneSubject')->info('create resource data '.$clonedResource->resource_slug);
        switch ($clonedResource->resource_slug) {
            case (LearningResourcesEnums::Audio):
                $this->createAudioDataResource($clonedResource->id, $oldResource->id);
                break;
            case (LearningResourcesEnums::Video):
                $this->createVideoDataResource($clonedResource->id, $oldResource->id);
                break;
            case (LearningResourcesEnums::PDF):
                $this->createPDFDataResource($clonedResource->id, $oldResource->id);
                break;
            case (LearningResourcesEnums::FLASH):
                $this->createFlashDataResource($clonedResource->id, $oldResource->id);
                break;
            case (LearningResourcesEnums::PICTURE):
                $this->createPictureDataResource($clonedResource->id, $oldResource->id);
                break;
            case (LearningResourcesEnums::PAGE):
                $this->createPageDataResource($clonedResource->id, $oldResource->id);
                break;
            case (LearningResourcesEnums::TRUE_FALSE):
                $this->createTrueFalseResource($clonedResource->id, $oldResource->id);
                break;
            case (LearningResourcesEnums::MULTI_CHOICE):
                $this->createMultipleChoiceResource($clonedResource->id, $oldResource->id);
                break;
            case (LearningResourcesEnums::MATCHING):
                $this->createMatchingResource($clonedResource->id, $oldResource->id);
                break;
            case (LearningResourcesEnums::MULTIPLE_MATCHING):
                $this->createMultiMatchingResource($clonedResource->id, $oldResource->id);
                break;
            case (LearningResourcesEnums::DRAG_DROP):
                $this->createDragDropResource($clonedResource->id, $oldResource->id);
                break;
            case (LearningResourcesEnums::COMPLETE):
                $this->createCompleteResource($clonedResource->id, $oldResource->id);
                break;
            case (LearningResourcesEnums::HOTSPOT):
                $this->createHotSpotResource($clonedResource->id, $oldResource->id);
                break;
        }
    }

    private function createAudioDataResource(int $clonedResourceId, int $oldResourceId)
    {
        $audioData = AudioData::where(
            'resource_subject_format_subject_id',
            $oldResourceId
        )->with('media')->withCount('media')->first();
        if ($audioData) {
            $audioCloned = AudioData::create([
                'description' => $audioData->description,
                'title' => $audioData->title,
                'resource_subject_format_subject_id' => $clonedResourceId,
                'audio_type' => $audioData->audio_type,
                'link' => $audioData->link,
            ]);
            if ($audioData->media_count > 0) {
                foreach ($audioData->media as $oldMedia) {
                    // Copy file
                    $newName = $oldMedia->filename;
                    if (File::exists(storage_path('app/public/uploads/large/'.$oldMedia->filename))) {
                        $newName = 'clone_'.time().'_.'.$oldMedia->extension;
                        File::copy(
                            storage_path('app/public/uploads/large/'.$oldMedia->filename),
                            storage_path('app/public/uploads/large/subject/audios/'.$newName)
                        );
                        $newName = 'subject/audios/'.$newName;
                    }
                    $audioCloned->media()->create([
                        'source_filename' => $oldMedia->source_filename,
                        'filename' => $newName,
                        'mime_type' => $oldMedia->mime_type,
                        'url' => $oldMedia->url,
                        'extension' => $oldMedia->extension,
                        'status' => $oldMedia->status,
                    ]);
                }
            }
        }
    }

    private function createVideoDataResource(int $clonedResourceId, int $oldResourceId)
    {
        $videoData = VideoData::where(
            'resource_subject_format_subject_id',
            $oldResourceId
        )->with('media')->withCount('media')->first();
        if ($videoData) {
            $videoCloned = VideoData::create([
                'description' => $videoData->description,
                'title' => $videoData->title,
                'resource_subject_format_subject_id' => $clonedResourceId,
                'video_type' => $videoData->video_type,
                'link' => $videoData->link,
            ]);
            if ($videoData->media_count > 0) {
                foreach ($videoData->media as $oldMedia) {
                    $newName = $oldMedia->filename;
                    if (File::exists(storage_path('app/public/uploads/large/'.$oldMedia->filename))) {
                        $newName = 'clone_'.time().'_.'.$oldMedia->extension;
                        File::copy(
                            storage_path('app/public/uploads/large/'.$oldMedia->filename),
                            storage_path('app/public/uploads/large/subject/videos/'.$newName)
                        );
                        $newName = 'subject/videos/'.$newName;
                    }
                    $videoCloned->media()->create([
                        'source_filename' => $oldMedia->source_filename,
                        'filename' => $newName,
                        'mime_type' => $oldMedia->mime_type,
                        'url' => $oldMedia->url,
                        'extension' => $oldMedia->extension,
                        'status' => $oldMedia->status
                    ]);
                }
            }
        }
    }

    private function createPDFDataResource(int $clonedResourceId, int $oldResourceId)
    {
        $pdfData = PdfData::where(
            'resource_subject_format_subject_id',
            $oldResourceId
        )->with('media')->withCount('media')->first();
        if ($pdfData) {
            $pdfCloned = PdfData::create([
                'description' => $pdfData->description,
                'title' => $pdfData->title,
                'resource_subject_format_subject_id' => $clonedResourceId,
                'pdf_type' => $pdfData->pdf_type,
                'link' => $pdfData->link,
            ]);
            if ($pdfData->media_count > 0) {
                foreach ($pdfData->media as $oldMedia) {
                    $newName = $oldMedia->filename;
                    if (File::exists(storage_path('app/public/uploads/large/'.$oldMedia->filename))) {
                        $newName = 'clone_'.time().'_.'.$oldMedia->extension;
                        File::copy(
                            storage_path('app/public/uploads/large/'.$oldMedia->filename),
                            storage_path('app/public/uploads/large/subject/pdfs/'.$newName)
                        );
                        $newName = 'subject/pdfs/'.$newName;
                    }
                    $pdfCloned->media()->create([
                        'source_filename' => $oldMedia->source_filename,
                        'filename' => $newName,
                        'mime_type' => $oldMedia->mime_type,
                        'url' => $oldMedia->url,
                        'extension' => $oldMedia->extension,
                        'status' => $oldMedia->status,
                    ]);
                }
            }
        }
    }

    private function createFlashDataResource(int $clonedResourceId, int $oldResourceId)
    {
        $flashData = FlashData::where(
            'resource_subject_format_subject_id',
            $oldResourceId
        )->with('media')->withCount('media')->first();
        if ($flashData) {
            $flashCloned = FlashData::create([
                'description' => $flashData->description,
                'title' => $flashData->title,
                'resource_subject_format_subject_id' => $clonedResourceId,
                'link' => $flashData->link,
            ]);
            if ($flashData->media_count > 0) {
                foreach ($flashData->media as $oldMedia) {
                    $newName = $oldMedia->filename;
                    if (File::exists(storage_path('app/public/uploads/large/'.$oldMedia->filename))) {
                        $newName = 'clone_'.time().'_.'.$oldMedia->extension;
                        File::copy(
                            storage_path('app/public/uploads/large/'.$oldMedia->filename),
                            storage_path('app/public/uploads/large/subject/flashes/'.$newName)
                        );
                        $newName = 'subject/flashes/'.$newName;
                    }
                    $flashCloned->media()->create([
                        'source_filename' => $oldMedia->source_filename,
                        'filename' => $newName,
                        'mime_type' => $oldMedia->mime_type,
                        'url' => $oldMedia->url,
                        'extension' => $oldMedia->extension,
                        'status' => $oldMedia->status,
                    ]);
                }
            }
        }
    }

    private function createPictureDataResource(int $clonedResourceId, int $oldResourceId)
    {
        $pictureData = PictureData::where(
            'resource_subject_format_subject_id',
            $oldResourceId
        )->with('media')->withCount('media')->first();
        if ($pictureData) {
            $pictureCloned = PictureData::create([
                'title' => $pictureData->title,
                'description' => $pictureData->description,
                'resource_subject_format_subject_id' => $clonedResourceId,
            ]);
            if ($pictureData->media_count > 0) {
                foreach ($pictureData->media as $oldMedia) {
                    $newName = $oldMedia->filename;
                    if (File::exists(storage_path('app/public/uploads/large/'.$oldMedia->filename))) {
                        $newName = 'clone_'.time().'_.'.$oldMedia->extension;
                        File::copy(
                            storage_path('app/public/uploads/large/'.$oldMedia->filename),
                            storage_path('app/public/uploads/large/subject/pictures/'.$newName)
                        );
                        $newName = 'subject/pictures/'.$newName;
                    }
                    $pictureCloned->media()->create([
                        'source_filename' => $oldMedia->source_filename,
                        'filename' => $newName,
                        'mime_type' => $oldMedia->mime_type,
                        'url' => $oldMedia->url,
                        'extension' => $oldMedia->extension,
                        'status' => $oldMedia->status,
                    ]);
                }
            }
        }
    }

    private function createPageDataResource(int $clonedResourceId, int $oldResourceId)
    {
        $pageData = PageData::where(
            'resource_subject_format_subject_id',
            $oldResourceId
        )->first();
        if ($pageData) {
            PageData::create([
                'title' => $pageData->title,
                'page' => $pageData->page,
                'resource_subject_format_subject_id' => $clonedResourceId,
            ]);
        }
    }

    private function createTrueFalseResource(int $clonedResourceId, int $oldResourceId)
    {
        $trueFalseData = TrueFalseData::where(
            'resource_subject_format_subject_id',
            $oldResourceId
        )->with('questions')->first();
        if ($trueFalseData) {
            $clonedTrueFalseData = TrueFalseData::create([
                'description' => $trueFalseData->description,
                'resource_subject_format_subject_id' => $clonedResourceId,
                'true_false_type' => $trueFalseData->true_false_type,
                'question_type' => $trueFalseData->question_type,
            ]);

            foreach ($trueFalseData->questions as $question) {
                // question text
                $clonedQuestion = $clonedTrueFalseData->questions()->create([
                    'text' => $question->text,
                    'image' => $question->image,
                    'is_true' => $question->is_true,
                    'question_feedback' => $question->question_feedback,
                    'time_to_solve' => $question->time_to_solve,
                ]);

                // question media
                if ($question->media_count > 0) {
                    foreach ($question->media as $oldMedia) {
                        // Copy file
                        $newName = $oldMedia->filename;
                        if (File::exists(storage_path('app/public/uploads/large/'.$oldMedia->filename))) {
                            $newName = 'clone_'.time().'_.'.$oldMedia->extension;
                            File::copy(
                                storage_path('app/public/uploads/large/'.$oldMedia->filename),
                                storage_path('app/public/uploads/large/subject/true_false/'.$newName)
                            );
                            $newName = 'subject/true_false/'.$newName;
                        }
                        $clonedQuestion->media()->create([
                            'source_filename' => $oldMedia->source_filename,
                            'filename' => $newName,
                            'mime_type' => $oldMedia->mime_type,
                            'url' => $oldMedia->url,
                            'extension' => $oldMedia->extension,
                            'status' => $oldMedia->status,
                        ]);
                    }
                }

                // question options
                $options = $question->options()->get();

                foreach ($options as $option) {
                    $clonedQuestion->options()->create([
                        'option' => $option->option,
                        'is_correct_answer' => $option->is_correct_answer,
                    ]);
                }
            }
        }
    }

    private function createMultipleChoiceResource(int $clonedResourceId, int $oldResourceId)
    {
        $multipleChoiceData = MultipleChoiceData::where(
            'resource_subject_format_subject_id',
            $oldResourceId
        )->with('questions')->first();
        if ($multipleChoiceData) {
            $clonedMultipleChoiceData = MultipleChoiceData::create([
                'description' => $multipleChoiceData->description,
                'resource_subject_format_subject_id' => $clonedResourceId,
                'multiple_choice_type' => $multipleChoiceData->multiple_choice_type,
            ]);

            foreach ($multipleChoiceData->questions as $question) {
                // question text
                $clonedQuestion = $clonedMultipleChoiceData->questions()->create([
                    'question' => $question->question,
                    'url' => $question->url,
                    'question_feedback' => $question->question_feedback,
                    'time_to_solve' => $question->time_to_solve,
                    'res_multiple_choice_data_id' => $clonedMultipleChoiceData->id,
                ]);

                // question media
                if ($question->media_count > 0) {
                    foreach ($question->media as $oldMedia) {
                        // Copy file
                        $newName = $oldMedia->filename;
                        if (File::exists(storage_path('app/public/uploads/large/'.$oldMedia->filename))) {
                            $newName = 'clone_'.time().'_.'.$oldMedia->extension;
                            File::copy(
                                storage_path('app/public/uploads/large/'.$oldMedia->filename),
                                storage_path('app/public/uploads/large/subject/multiple_choice/'.$newName)
                            );
                            $newName = 'subject/multiple_choice/'.$newName;
                        }
                        $clonedQuestion->media()->create([
                            'source_filename' => $oldMedia->source_filename,
                            'filename' => $newName,
                            'mime_type' => $oldMedia->mime_type,
                            'url' => $oldMedia->url,
                            'extension' => $oldMedia->extension,
                            'status' => $oldMedia->status,
                        ]);
                    }
                }

                // question options
                $options = $question->options()->get();

                foreach ($options as $option) {
                    $clonedQuestion->options()->create([
                        'answer' => $option->answer,
                        'is_correct_answer' => $option->is_correct_answer
                    ]);
                }
            }
        }
    }

    private function createDragDropResource(int $clonedResourceId, int $oldResourceId)
    {
        $dragDropData = DragDropData::where(
            'resource_subject_format_subject_id',
            $oldResourceId
        )->with('questions')->with('options')->first();
        if ($dragDropData) {
            $clonedDragDropData = DragDropData::create([
                'description' => $dragDropData->description,
                'resource_subject_format_subject_id' => $clonedResourceId,
                'drag_drop_type' => $dragDropData->drag_drop_type,
                'question_feedback' => $dragDropData->question_feedback,
                'time_to_solve' => $dragDropData->time_to_solve,
            ]);

            $optionArray = [];
            foreach ($dragDropData->options as $option) {
                $optionCloned = $clonedDragDropData->options()->create([
                    'option' => $option->option,
                ]);
                $optionArray[$option->id] = $optionCloned->id;
            }

            foreach ($dragDropData->questions as $question) {
                if (is_null($question->correct_option_id)) {
                    $optionCorrect = null;
                } else {
                    $optionCorrect = isset($optionArray[$question->correct_option_id]) ? $optionArray[$question->correct_option_id] : null;
                }
                $clonedQuestion = $clonedDragDropData->questions()->create([
                    'question' => $question->question,
                    'image' => $question->image,
                    'correct_option_id' => $optionCorrect,
                ]);
                // question media
                if ($question->media_count > 0) {
                    foreach ($question->media as $oldMedia) {
                        // Copy file
                        $newName = $oldMedia->filename;
                        if (File::exists(storage_path('app/public/uploads/large/'.$oldMedia->filename))) {
                            $newName = 'clone_'.time().'_.'.$oldMedia->extension;
                            File::copy(
                                storage_path('app/public/uploads/large/'.$oldMedia->filename),
                                storage_path('app/public/uploads/large/subject/drag_drop/'.$newName)
                            );
                            $newName = 'subject/drag_drop/'.$newName;
                        }
                        $clonedQuestion->media()->create([
                            'source_filename' => $oldMedia->source_filename,
                            'filename' => $newName,
                            'mime_type' => $oldMedia->mime_type,
                            'url' => $oldMedia->url,
                            'extension' => $oldMedia->extension,
                            'status' => $oldMedia->status,
                        ]);
                    }
                }
            }
        }
    }

    private function createMatchingResource(int $clonedResourceId, int $oldResourceId)
    {
        $matchingData = MatchingData::where(
            'resource_subject_format_subject_id',
            $oldResourceId
        )->with('questions')->with('options')->first();
        if ($matchingData) {
            $clonedMatchingData = MatchingData::create([
                'description' => $matchingData->description,
                'question_feedback' => $matchingData->question_feedback,
                'resource_subject_format_subject_id' => $clonedResourceId,
                'time_to_solve' => $matchingData->time_to_solve,
            ]);

            $questionsArray = [];
            foreach ($matchingData->questions as $question) {
                // question text
                $clonedQuestion = $clonedMatchingData->questions()->create([
                    'text' => $question->text,
                ]);
                // question media
                if ($question->media_count > 0) {
                    foreach ($question->media as $oldMedia) {
                        // Copy file
                        $newName = $oldMedia->filename;
                        if (File::exists(storage_path('app/public/uploads/large/'.$oldMedia->filename))) {
                            $newName = 'clone_'.time().'_.'.$oldMedia->extension;
                            File::copy(
                                storage_path('app/public/uploads/large/'.$oldMedia->filename),
                                storage_path('app/public/uploads/large/subject/matching/'.$newName)
                            );
                            $newName = 'subject/matching/'.$newName;
                        }
                        $clonedQuestion->media()->create([
                            'source_filename' => $oldMedia->source_filename,
                            'filename' => $newName,
                            'mime_type' => $oldMedia->mime_type,
                            'url' => $oldMedia->url,
                            'extension' => $oldMedia->extension,
                            'status' => $oldMedia->status,
                        ]);
                    }
                }
                $questionsArray[$question->id] = $clonedQuestion->id;
            }

            foreach ($matchingData->options as $option) {
                if (is_null($option->res_matching_question_id)) {
                    $newQuestionId = null;
                } else {
                    $newQuestionId = isset($questionsArray[$option->res_matching_question_id]) ? $questionsArray[$option->res_matching_question_id] : null;
                }
                $clonedMatchingData->options()->create([
                    'option' => $option->option,
                    'res_matching_question_id' => $newQuestionId,
                ]);
            }
        }
    }

    private function createMultiMatchingResource(int $clonedResourceId, int $oldResourceId)
    {
        $multiMatchingData = MultiMatchingData::where(
            'resource_subject_format_subject_id',
            $oldResourceId
        )->with('questions')->with('options')->first();
        if ($multiMatchingData) {
            $clonedMultiMatchingData = MultiMatchingData::create([
                'description' => $multiMatchingData->description,
                'question_feedback' => $multiMatchingData->question_feedback,
                'resource_subject_format_subject_id' => $clonedResourceId,
                'time_to_solve' => $multiMatchingData->time_to_solve,
            ]);

            foreach ($multiMatchingData->questions as $question) {
                $clonedQuestion = $clonedMultiMatchingData->questions()->create([
                    'text' => $question->text,
                ]);

                // question media
                if ($question->media_count > 0) {
                    foreach ($question->media as $oldMedia) {
                        // Copy file
                        $newName = $oldMedia->filename;
                        if (File::exists(storage_path('app/public/uploads/large/'.$oldMedia->filename))) {
                            $newName = 'clone_'.time().'_.'.$oldMedia->extension;
                            File::copy(
                                storage_path('app/public/uploads/large/'.$oldMedia->filename),
                                storage_path('app/public/uploads/large/subject/multi_matching/'.$newName)
                            );
                            $newName = 'subject/multi_matching/'.$newName;
                        }
                        $clonedQuestion->media()->create([
                            'source_filename' => $oldMedia->source_filename,
                            'filename' => $newName,
                            'mime_type' => $oldMedia->mime_type,
                            'url' => $oldMedia->url,
                            'extension' => $oldMedia->extension,
                            'status' => $oldMedia->status,
                        ]);
                    }
                }

                if ($question->options()->count() > 0) {
                    $optionsIds = [];
                    foreach ($question->options()->get() as $option) {
                        $clonedOption = $clonedMultiMatchingData->options()->create([
                            'option' => $option->option,
                        ]);
                        $optionsIds[] = $clonedOption->id;
                    }
                    $clonedQuestion->options()->sync($optionsIds);
                }
            }

            if ($multiMatchingData->options()->doesntHave('questions')->count() > 0) {
                foreach ($multiMatchingData->options()->doesntHave('questions')->get() as $option) {
                    $clonedMultiMatchingData->options()->create([
                        'option' => $option->option,
                    ]);
                }
            }
        }
    }

    private function createCompleteResource(int $clonedResourceId, int $oldResourceId)
    {
        $completeData = CompleteData::where(
            'resource_subject_format_subject_id',
            $oldResourceId
        )->with('questions')->first();

        if ($completeData) {
            $clonedCompleteData = CompleteData::create([
                'description' => $completeData->description,
                'resource_subject_format_subject_id' => $clonedResourceId,
            ]);

            foreach ($completeData->questions as $question) {
                $clonedQuestion = $clonedCompleteData->questions()->create([
                    'question' => $question->question,
                    'question_feedback' => $question->question_feedback,
                    'res_complete_data_id' => $question->res_complete_data_id,
                ]);

                // the answer
                $answer = $question->answer()->first();

                $clonedQuestion->answer()->create([
                    'answer' => $answer->answer,
                    'res_complete_question_id' => $answer->res_complete_question_id
                ]);

                // the accepted answers
                $options = $question->options()->get();
                foreach ($options as $option) {
                    $clonedQuestion->options()->create([
                        'answer' => $option->answer,
                        'res_complete_question_id' => $option->res_complete_question_id,
                    ]);
                }
            }
        }
    }

    private function createHotSpotResource(int $clonedResourceId, int $oldResourceId)
    {
        $hotSpotData = HotSpotData::where(
            'resource_subject_format_subject_id',
            $oldResourceId
        )->with('questions')->first();

        if ($hotSpotData) {
            $clonedHotSpotData = HotSpotData::create([
                'description' => $hotSpotData->description,
                'resource_subject_format_subject_id' => $clonedResourceId,
            ]);

            foreach ($hotSpotData->questions as $question) {
                $clonedQuestion = $clonedHotSpotData->questions()->create([
                    'question' => $question->question,
                    'question_feedback' => $question->question_feedback,
                    'image_width' => $question->image_width,
                    'time_to_solve' => $question->time_to_solve,
                    'res_hot_spot_data_id' => $question->res_hot_spot_data_id,
                ]);

                // the answer
                $answer = $question->answer()->first();

                $clonedQuestion->answer()->create([
                    'answer' => json_encode($answer->answer),
                    'res_complete_question_id' => $answer->res_complete_question_id
                ]);

                foreach ($question->media as $oldMedia) {
                    // Copy file
                    $newName = $oldMedia->filename;
                    if (File::exists(storage_path('app/public/uploads/large/'.$oldMedia->filename))) {
                        $newName = 'clone_'.time().'_.'.$oldMedia->extension;
                        File::copy(
                            storage_path('app/public/uploads/large/'.$oldMedia->filename),
                            storage_path('app/public/uploads/large/subject/true_false/'.$newName)
                        );
                        $newName = 'subject/true_false/'.$newName;
                    }
                    $clonedQuestion->media()->create([
                        'source_filename' => $oldMedia->source_filename,
                        'filename' => $newName,
                        'mime_type' => $oldMedia->mime_type,
                        'url' => $oldMedia->url,
                        'extension' => $oldMedia->extension,
                        'status' => $oldMedia->status,
                    ]);
                }

            }
        }
    }
}
