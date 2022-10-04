<?php

namespace App\OurEdu\Subjects\ContentAuthor\Transformers;

use App\OurEdu\Subjects\SME\Transformers\SubjectFormatSubjectTransformer;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\Subjects\Models\Subject;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\ResourceSubjectFormats\Models\Pdf\PdfData;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\ResourceSubjectFormats\Models\Page\PageData;
use App\OurEdu\ResourceSubjectFormats\Models\Audio\AudioData;
use App\OurEdu\ResourceSubjectFormats\Models\Flash\FlashData;
use App\OurEdu\ResourceSubjectFormats\Models\Matching\MatchingData;
use App\OurEdu\ResourceSubjectFormats\Models\HotSpot\HotSpotData;
use App\OurEdu\ResourceSubjectFormats\Models\MultiMatching\MultiMatchingData;
use App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceData;
use App\OurEdu\ResourceSubjectFormats\Models\Video\VideoData;
use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\ResourceSubjectFormats\Models\Picture\PictureData;
use App\OurEdu\ResourceSubjectFormats\Models\Complete\CompleteData;
use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropData;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseData;
use App\OurEdu\ResourceSubjectFormats\Transformers\PdfDataTransformer;
use App\OurEdu\ResourceSubjectFormats\Transformers\PageDataTransformer;
use App\OurEdu\ResourceSubjectFormats\Transformers\AudioDataTransformer;
use App\OurEdu\ResourceSubjectFormats\Transformers\FlashDataTransformer;
use App\OurEdu\ResourceSubjectFormats\Transformers\HotSpotDataTransformer;
use App\OurEdu\ResourceSubjectFormats\Transformers\VideoDataTransformer;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use App\OurEdu\ResourceSubjectFormats\Transformers\PictureDataTransformer;
use App\OurEdu\ResourceSubjectFormats\Transformers\CompleteDataTransformer;
use App\OurEdu\ResourceSubjectFormats\Transformers\DragDropDataTransformer;
use App\OurEdu\ResourceSubjectFormats\Transformers\MatchingDataTransformer;
use App\OurEdu\ResourceSubjectFormats\Transformers\TrueFalseDataTransformer;
use App\OurEdu\ResourceSubjectFormats\Transformers\MultiMatchingDataTransformer;
use App\OurEdu\ResourceSubjectFormats\Transformers\MultipleChoiceDataTransformer;
use App\OurEdu\LearningResources\Transformers\LearningResourceAcceptCriteriaGetTransformer;

class ResourceSubjectFormatSubjectTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'learningResourceAcceptCriteria',
        'resourceSubjectFormatSubjectData',

    ];
    protected array $availableIncludes = [
        'subjectFormatSubjects',
        'actions'
    ];

    private $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public static function originalAttribute($index)
    {
        $attributes = [
            'id' => 'id',
            'date' => 'date',
            'SN' => 'SN'
        ];
        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

    public static function transformedAttribute($index)
    {
        $attributes = [
            'id' => 'id',
            'date' => 'date',
            'SN' => 'SN'
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

    /**
     * @param ResourceSubjectFormatSubject $resourceSubjectFormatSubject
     * @return array
     */
    public function transform(ResourceSubjectFormatSubject $resourceSubjectFormatSubject)
    {
        return [
            'id' => (int)$resourceSubjectFormatSubject->id,
//            'accept_criteria' => json_decode($resourceSubjectFormatSubject->accept_criteria),
            'resource_id' => (string)$resourceSubjectFormatSubject->resource->id,
            'resource_slug' => (string)$resourceSubjectFormatSubject->resource->slug,
            'is_active' => (bool)$resourceSubjectFormatSubject->is_active,
//            'is_editable' => $resourceSubjectFormatSubject->task ? false : true,
            'is_editable' => (bool)$resourceSubjectFormatSubject->is_editable,
            'resource_rule' => getResourceRules($resourceSubjectFormatSubject->resource->slug)

        ];
    }

    public function includeSubjectFormatSubjects(Subject $subject)
    {
//        $page = request('rcsp_page');
        $subjectFormatSubjects = $subject->subjectFormatSubject()->orderBy('list_order_key', 'asc')->get();

        if (count($subjectFormatSubjects) > 0) {
            return $this->collection(
                $subjectFormatSubjects,
                new SubjectFormatSubjectTransformer(),
                ResourceTypesEnums::SUBJECT_FORMAT_SUBJECT
            );
        }
    }

    public function includeLearningResourceAcceptCriteria(ResourceSubjectFormatSubject $resourceSubjectFormatSubject)
    {
        $acceptCriteria = json_decode($resourceSubjectFormatSubject->accept_criteria, true);
        if (is_array($acceptCriteria)) {
            return $this->item(
                $acceptCriteria,
                new LearningResourceAcceptCriteriaGetTransformer(),
                ResourceTypesEnums::LEARNING_RESOURCE_ACCEPT_CRITERIA_FIELD
            );
        }
    }

    public function includeResourceSubjectFormatSubjectData(ResourceSubjectFormatSubject $resourceSubjectFormatSubject)
    {
        switch ($resourceSubjectFormatSubject->resource_slug) {

            case LearningResourcesEnums::TRUE_FALSE:
                $trueFalseData = TrueFalseData::where(
                    'resource_subject_format_subject_id',
                    $resourceSubjectFormatSubject->id
                )
                    ->with('questions.options')->first();
                if ($trueFalseData) {
                    return $this->item(
                        $trueFalseData,
                        new TrueFalseDataTransformer(),
                        ResourceTypesEnums::RESOURCE_SUBJECT_FORMAT_SUBJECT_DATA
                    );
                }
                break;
            case LearningResourcesEnums::MULTI_CHOICE:
                $multipleChoiceData = MultipleChoiceData::where(
                    'resource_subject_format_subject_id',
                    $resourceSubjectFormatSubject->id
                )
                    ->with('questions.options')->first();
                if ($multipleChoiceData) {
                    return $this->item(
                        $multipleChoiceData,
                        new MultipleChoiceDataTransformer(),
                        ResourceTypesEnums::RESOURCE_SUBJECT_FORMAT_SUBJECT_DATA
                    );
                }
                break;
            case LearningResourcesEnums::Video:
                $videoData = VideoData::where(
                    'resource_subject_format_subject_id',
                    $resourceSubjectFormatSubject->id
                )->first();
                if ($videoData) {
                    return $this->item(
                        $videoData,
                        new VideoDataTransformer(),
                        ResourceTypesEnums::RESOURCE_SUBJECT_FORMAT_SUBJECT_DATA
                    );
                }
                break;
            case LearningResourcesEnums::DRAG_DROP:
                $dragDropData = DragDropData::where(
                    'resource_subject_format_subject_id',
                    $resourceSubjectFormatSubject->id
                )->first();
                if ($dragDropData) {
                    return $this->item(
                        $dragDropData,
                        new DragDropDataTransformer(),
                        ResourceTypesEnums::RESOURCE_SUBJECT_FORMAT_SUBJECT_DATA
                    );
                }

                break;
            case LearningResourcesEnums::MATCHING:

                $matchingData = MatchingData::where(
                    'resource_subject_format_subject_id',
                    $resourceSubjectFormatSubject->id
                )->first();
                if ($matchingData) {
                    return $this->item(
                        $matchingData,
                        new MatchingDataTransformer(),
                        ResourceTypesEnums::RESOURCE_SUBJECT_FORMAT_SUBJECT_DATA
                    );
                }
                break;
            case LearningResourcesEnums::MULTIPLE_MATCHING:
                $multiMatchingDropData = MultiMatchingData::where(
                    'resource_subject_format_subject_id',
                    $resourceSubjectFormatSubject->id
                )->first();
                if ($multiMatchingDropData) {
                    return $this->item(
                        $multiMatchingDropData,
                        new MultiMatchingDataTransformer(),
                        ResourceTypesEnums::RESOURCE_SUBJECT_FORMAT_SUBJECT_DATA
                    );
                }
                break;
            case LearningResourcesEnums::PAGE:

                $pageData = PageData::where(
                    'resource_subject_format_subject_id',
                    $resourceSubjectFormatSubject->id
                )->first();

                if ($pageData) {
                    return $this->item(
                        $pageData,
                        new PageDataTransformer(),
                        ResourceTypesEnums::RESOURCE_SUBJECT_FORMAT_SUBJECT_DATA
                    );
                }

                break;
            case LearningResourcesEnums::Audio:
                $audioData = AudioData::where(
                    'resource_subject_format_subject_id',
                    $resourceSubjectFormatSubject->id
                )->first();

                if ($audioData) {
                    return $this->item(
                        $audioData,
                        new AudioDataTransformer(),
                        ResourceTypesEnums::RESOURCE_SUBJECT_FORMAT_SUBJECT_DATA
                    );
                }
                break;
            case LearningResourcesEnums::PDF:
                $pdfData = PdfData::where(
                    'resource_subject_format_subject_id',
                    $resourceSubjectFormatSubject->id
                )->first();
                if ($pdfData) {
                    return $this->item(
                        $pdfData,
                        new PdfDataTransformer(),
                        ResourceTypesEnums::RESOURCE_SUBJECT_FORMAT_SUBJECT_DATA
                    );
                }
                break;
            case LearningResourcesEnums::PICTURE:
                $pictureData = PictureData::where(
                    'resource_subject_format_subject_id',
                    $resourceSubjectFormatSubject->id
                )->first();
                if ($pictureData) {
                    return $this->item(
                        $pictureData,
                        new PictureDataTransformer(),
                        ResourceTypesEnums::RESOURCE_SUBJECT_FORMAT_SUBJECT_DATA
                    );
                }
                break;

            case LearningResourcesEnums::FLASH:
                $flashData = FlashData::where(
                    'resource_subject_format_subject_id',
                    $resourceSubjectFormatSubject->id
                )->first();
                if ($flashData) {
                    return $this->item(
                        $flashData,
                        new FlashDataTransformer(),
                        ResourceTypesEnums::RESOURCE_SUBJECT_FORMAT_SUBJECT_DATA
                    );
                }
                break;

            case LearningResourcesEnums::HOTSPOT:
                $hotSpotData = HotSpotData::where(
                    'resource_subject_format_subject_id',
                    $resourceSubjectFormatSubject->id
                )->orderBy('id', 'desc')->limit(1)->first();
                if ($hotSpotData) {
                    return $this->item(
                        $hotSpotData,
                        new HotSpotDataTransformer(),
                        ResourceTypesEnums::RESOURCE_SUBJECT_FORMAT_SUBJECT_DATA
                    );
                }
                break;


            case LearningResourcesEnums::COMPLETE:
                $completeData = CompleteData::where(
                    'resource_subject_format_subject_id',
                    $resourceSubjectFormatSubject->id
                )->first();
                if ($completeData) {
                    return $this->item(
                        $completeData,
                        new CompleteDataTransformer(),
                        ResourceTypesEnums::RESOURCE_SUBJECT_FORMAT_SUBJECT_DATA
                    );
                }
                break;
        }
    }

    public function includeActions($resourceSubjectFormatSubject)
    {
        $actions = [];
        if (auth()->user()->type == UserEnums::CONTENT_AUTHOR_TYPE) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute(
                    'api.contentAuthor.subjects.fillResource',
                    ['resourceId' => $resourceSubjectFormatSubject->id]
                ),
                'label' => trans('task.Fill Task'),
                'key' => APIActionsEnums::FILL_RESOURCE,
                'method' => 'PUT'
            ];

            //@todo where is related_task
            if ($resourceSubjectFormatSubject->related_task) {
                $task = $resourceSubjectFormatSubject->related_task;
                if ($task->is_done == 0 && $task->contentAuthors()->where('content_authors.id', auth()->user()->contentAuthor->id)->exists()) {
                    $actions[] = [
                        'endpoint_url' => buildScopeRoute('api.contentAuthor.subjects.markTaskAsDone', ['id' => $task->id]),
                        'label' => trans('task.Done'),
                        'key' => APIActionsEnums::MARK_TASK_AS_DONE,
                        'method' => 'GET'
                    ];
                }
            }
        }
        return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
    }
}
