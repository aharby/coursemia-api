<?php

namespace App\OurEdu\ResourceSubjectFormats\Models;

use App\OurEdu\BaseApp\BaseModel;
use App\OurEdu\BaseApp\Traits\CreatedBy;
use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\LearningResources\Resource;
use App\OurEdu\QuestionReport\Models\QuestionReport;
use App\OurEdu\ResourceSubjectFormats\Models\Audio\AudioData;
use App\OurEdu\ResourceSubjectFormats\Models\Complete\CompleteData;
use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropData;
use App\OurEdu\ResourceSubjectFormats\Models\Essay\EssayData;
use App\OurEdu\ResourceSubjectFormats\Models\Flash\FlashData;
use App\OurEdu\ResourceSubjectFormats\Models\HotSpot\HotSpotData;
use App\OurEdu\ResourceSubjectFormats\Models\Matching\MatchingData;
use App\OurEdu\ResourceSubjectFormats\Models\MultiMatching\MultiMatchingData;
use App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceData;
use App\OurEdu\ResourceSubjectFormats\Models\Page\PageData;
use App\OurEdu\ResourceSubjectFormats\Models\Pdf\PdfData;
use App\OurEdu\ResourceSubjectFormats\Models\Picture\PictureData;
use App\OurEdu\ResourceSubjectFormats\Models\Progress\ResourceProgressStudent;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseData;
use App\OurEdu\ResourceSubjectFormats\Models\Video\VideoData;
use App\OurEdu\ResourceSubjectFormats\Observers\ResourceSubjectFormatSubjectObserver;
use App\OurEdu\Scopes\ResourceSubjectFormatSubjectActiveParentScope;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use App\OurEdu\Subjects\Models\SubModels\SubjectTime;
use App\OurEdu\Subjects\Models\SubModels\Task;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\OurEdu\QuestionReport\Models\QuestionReportTask;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ResourceSubjectFormatSubject extends BaseModel
{
    use SoftDeletes, CreatedBy,HasFactory;

    protected $table = 'resource_subject_format_subject';

    protected $fillable = [
        'accept_criteria',
        'resource_id',
        'resource_slug',
        'subject_format_subject_id',
        'is_active',
        'created_by',
        'slug',
        'is_editable',
        'list_order_key',
        'total_points',
        'subject_id',
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new ResourceSubjectFormatSubjectActiveParentScope());
        ResourceSubjectFormatSubject::observe(ResourceSubjectFormatSubjectObserver::class);
        static::deleting(function (self $resourceSubjectFormatSubject) {
//            $resources = $resourceSubjectFormatSubject->resource()->get();
//            foreach ($resources as $resource) {
//                $resource->delete();
//            }

            $audioData = $resourceSubjectFormatSubject->audioData()->get();
            foreach ($audioData as $item) {
                $item->delete();
            }

            $completeData = $resourceSubjectFormatSubject->completeData()->get();
            foreach ($completeData as $item) {
                $item->delete();
            }

            $dragDropData = $resourceSubjectFormatSubject->dragDropData()->get();
            foreach ($dragDropData as $item) {
                $item->delete();
            }

            $essayData = $resourceSubjectFormatSubject->essayData()->get();
            foreach ($essayData as $item) {
                $item->delete();
            }

            $flashData = $resourceSubjectFormatSubject->flashData()->get();
            foreach ($flashData as $item) {
                $item->delete();
            }

            $hotSpotData = $resourceSubjectFormatSubject->hotSpotData()->get();
            foreach ($hotSpotData as $item) {
                $item->delete();
            }

            $matchingData = $resourceSubjectFormatSubject->matchingData()->get();
            foreach ($matchingData as $item) {
                $item->delete();
            }

            $multiMatchingData = $resourceSubjectFormatSubject->multiMatchingData()->get();
            foreach ($multiMatchingData as $item) {
                $item->delete();
            }

            $multipleChoiceData = $resourceSubjectFormatSubject->multipleChoiceData()->get();
            foreach ($multipleChoiceData as $item) {
                $item->delete();
            }

            $pageData = $resourceSubjectFormatSubject->pageData()->get();
            foreach ($pageData as $item) {
                $item->delete();
            }

            $pdfData = $resourceSubjectFormatSubject->pdfData()->get();
            foreach ($pdfData as $item) {
                $item->delete();
            }

            $pictureData = $resourceSubjectFormatSubject->pictureData()->get();
            foreach ($pictureData as $item) {
                $item->delete();
            }

            $trueFalseData = $resourceSubjectFormatSubject->trueFalseData()->get();
            foreach ($trueFalseData as $item) {
                $item->delete();
            }

            $videoData = $resourceSubjectFormatSubject->videoData()->get();
            foreach ($videoData as $item) {
                $item->delete();
            }

        });
    }

    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    public function task()
    {
        return $this->hasOne(Task::class, 'resource_subject_format_subject_id');
    }

    public function subjectFormatSubject()
    {
        return $this->belongsTo(SubjectFormatSubject::class, 'subject_format_subject_id');
    }

    public function activeTasks()
    {
        return $this->hasMany(Task::class, 'resource_subject_format_subject_id')->active();
    }

    public function activeReportTasks()
    {
        return $this->hasMany(QuestionReportTask::class, 'resource_subject_format_subject_id')->active();
    }

    public function reports()
    {
        return $this->morphMany('App\OurEdu\Reports\Report','reportable');
    }

    public function time()
    {
        return $this->morphMany(SubjectTime::class ,'timable');
    }

    public function audioData()
    {
        return $this->hasMany(AudioData::class, "resource_subject_format_subject_id");
    }

    public function completeData()
    {
        return $this->hasMany(CompleteData::class, "resource_subject_format_subject_id");
    }

    public function dragDropData()
    {
        return $this->hasMany(DragDropData::class, "resource_subject_format_subject_id");
    }

    public function essayData()
    {
        return $this->hasMany(EssayData::class, "resource_subject_format_subject_id");
    }

    public function flashData()
    {
        return $this->hasMany(FlashData::class, "resource_subject_format_subject_id");
    }

    public function hotSpotData()
    {
        return $this->hasMany(HotSpotData::class, "resource_subject_format_subject_id");
    }

    public function matchingData()
    {
        return $this->hasMany(MatchingData::class, "resource_subject_format_subject_id");
    }

    public function multiMatchingData()
    {
        return $this->hasMany(MultiMatchingData::class, "resource_subject_format_subject_id");
    }

    public function multipleChoiceData()
    {
        return $this->hasMany(MultipleChoiceData::class, "resource_subject_format_subject_id");
    }

    public function pageData()
    {
        return $this->hasMany(PageData::class, "resource_subject_format_subject_id");
    }

    public function pdfData()
    {
        return $this->hasMany(PdfData::class, "resource_subject_format_subject_id");
    }

    public function pictureData()
    {
        return $this->hasMany(PictureData::class, "resource_subject_format_subject_id");
    }

    public function trueFalseData()
    {
        return $this->hasMany(TrueFalseData::class, "resource_subject_format_subject_id");
    }

    public function videoData()
    {
        return $this->hasMany(VideoData::class, "resource_subject_format_subject_id");
    }

    function students()
    {
      return $this->hasMany(ResourceProgressStudent::class, "resource_id");
    }
    public function pageDetails(){
        return $this->hasOne(PageData::class, 'resource_subject_format_subject_id');
    }
    public function audioDetails(){
        return $this->hasOne(AudioData::class, 'resource_subject_format_subject_id');
    }
    public function pictureDetails(){
        return $this->hasOne(PictureData::class, 'resource_subject_format_subject_id');
    }
    public function pdfDetails(){
        return $this->hasOne(PdfData::class, 'resource_subject_format_subject_id');
    }
    public function flashDetails(){
        return $this->hasOne(FlashData::class, 'resource_subject_format_subject_id');
    }
    public function videoDetails(){
        return $this->hasOne(VideoData::class, 'resource_subject_format_subject_id');
    }
}
