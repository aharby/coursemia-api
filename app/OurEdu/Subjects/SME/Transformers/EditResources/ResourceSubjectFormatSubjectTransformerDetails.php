<?php


namespace App\OurEdu\Subjects\SME\Transformers\EditResources;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\LearningResources\Sme\Transformers\LearningResourceAcceptCriteriaGetTransformer;
use App\OurEdu\ResourceSubjectFormats\Models\Complete\CompleteData;
use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropData;
use App\OurEdu\ResourceSubjectFormats\Models\HotSpot\HotSpotData;
use App\OurEdu\ResourceSubjectFormats\Models\Matching\MatchingData;
use App\OurEdu\ResourceSubjectFormats\Models\MultiMatching\MultiMatchingData;
use App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceData;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseData;
use App\OurEdu\Subjects\SME\Transformers\EditResources\Resources\AudioTransformer;
use App\OurEdu\Subjects\SME\Transformers\EditResources\Resources\CompleteDataTransformer;
use App\OurEdu\Subjects\SME\Transformers\EditResources\Resources\DragDropDataTransformer;
use App\OurEdu\Subjects\SME\Transformers\EditResources\Resources\FlashTransformer;
use App\OurEdu\Subjects\SME\Transformers\EditResources\Resources\HotSpotDataTransformer;
use App\OurEdu\Subjects\SME\Transformers\EditResources\Resources\MatchingDataTransformer;
use App\OurEdu\Subjects\SME\Transformers\EditResources\Resources\MultiMatchingDataTransformer;
use App\OurEdu\Subjects\SME\Transformers\EditResources\Resources\MultipleChoiceDataTransformer;
use App\OurEdu\Subjects\SME\Transformers\EditResources\Resources\PageTransformer;
use App\OurEdu\Subjects\SME\Transformers\EditResources\Resources\PdfTransformer;
use App\OurEdu\Subjects\SME\Transformers\EditResources\Resources\PictureTransformer;
use App\OurEdu\Subjects\SME\Transformers\EditResources\Resources\TrueFalseDataTransformer;
use App\OurEdu\Subjects\SME\Transformers\EditResources\Resources\VideoTransformer;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\ResourceSubjectFormats\Models\Pdf\PdfData;
use App\OurEdu\ResourceSubjectFormats\Models\Page\PageData;
use App\OurEdu\ResourceSubjectFormats\Models\Audio\AudioData;
use App\OurEdu\ResourceSubjectFormats\Models\Flash\FlashData;
use App\OurEdu\ResourceSubjectFormats\Models\Video\VideoData;
use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\ResourceSubjectFormats\Models\Picture\PictureData;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;

class ResourceSubjectFormatSubjectTransformerDetails extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'resourceSubjectFormatSubjectData',
        'learningResourceAcceptCriteria',
        'actions'
    ];
    protected array $availableIncludes = [
    ];

    /**
     * @param ResourceSubjectFormatSubject $resourceSubjectFormatSubject
     * @return array
     */
    public function transform(ResourceSubjectFormatSubject $resourceSubjectFormatSubject)
    {
        return [
            'id' => (int)$resourceSubjectFormatSubject->id,
            'resource_slug' => (string)$resourceSubjectFormatSubject->resource_slug,
            'is_active' => (boolean)$resourceSubjectFormatSubject->is_active,
            'is_editable' => (boolean)$resourceSubjectFormatSubject->is_editable,
            'task_is_done' => $resourceSubjectFormatSubject->task->is_done ?? 0,
            'list_order_key' => $resourceSubjectFormatSubject->list_order_key,
        ];
    }

    public function includeResourceSubjectFormatSubjectData(ResourceSubjectFormatSubject $resourceSubjectFormatSubject)
    {
        switch ($resourceSubjectFormatSubject->resource_slug) {
            case LearningResourcesEnums::PAGE:
                $pageData = PageData::where(
                    'resource_subject_format_subject_id',
                    $resourceSubjectFormatSubject->id
                )->first();

                if ($pageData) {
                    return $this->item(
                        $pageData,
                        new PageTransformer(),
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
                        new AudioTransformer(),
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
                        new PdfTransformer(),
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
                        new PictureTransformer(),
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
                        new FlashTransformer(),
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
                        new VideoTransformer(),
                        ResourceTypesEnums::RESOURCE_SUBJECT_FORMAT_SUBJECT_DATA
                    );
                }
                break;
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

    public function includeActions(ResourceSubjectFormatSubject $resourceSubjectFormatSubject)
    {
        $actions = [];
        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.sme.subjects.put-edit-resource', ['resourceSubjectFormatId' => $resourceSubjectFormatSubject->id]),
            'label' => trans('app.Edit'),
            'method' => 'PUT',
            'key' => APIActionsEnums::EDIT_RESOURCE
        ];

        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.sme.subjects.pause-unpause.resource',
                ['resourceId' => $resourceSubjectFormatSubject->id]),
            'label' => $resourceSubjectFormatSubject->is_active ? trans('api.pause') : trans('api.un pause'),
            'method' => 'POST',
            'key' => $resourceSubjectFormatSubject->is_active ? APIActionsEnums::PAUSE : APIActionsEnums::UN_PAUSE
        ];


        if ($resourceSubjectFormatSubject->task) {
            $task = $resourceSubjectFormatSubject->task;
            if ($task->is_done == 0) {
                $actions[] = [
                    'endpoint_url' => buildScopeRoute('api.sme.subjects.post.sme.markTaskAsDone', ['task_id' => $task->id]),
                    'label' => trans('task.Done'),
                    'key' => APIActionsEnums::MARK_TASK_AS_DONE,
                    'method' => 'post'
                ];
            }
        }

        if($resourceSubjectFormatSubject->subjectFormatSubject)
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.sme.subjects.create-resource-structural', ['section' => $resourceSubjectFormatSubject->subjectFormatSubject->id]),
                'label' => trans('app.create resource structure'),
                'method' => 'PUT',
                'key' => APIActionsEnums::CREATE_RESOURCE
            ];


        return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);

    }
}
