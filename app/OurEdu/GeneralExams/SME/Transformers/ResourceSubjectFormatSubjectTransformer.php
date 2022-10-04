<?php

namespace App\OurEdu\GeneralExams\SME\Transformers;

use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\ResourceSubjectFormats\Models\HotSpot\HotSpotData;
use App\OurEdu\ResourceSubjectFormats\Models\Complete\CompleteData;
use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropData;
use App\OurEdu\ResourceSubjectFormats\Models\Matching\MatchingData;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseData;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use App\OurEdu\ResourceSubjectFormats\Transformers\HotSpotDataTransformer;
use App\OurEdu\ResourceSubjectFormats\Transformers\CompleteDataTransformer;
use App\OurEdu\ResourceSubjectFormats\Transformers\DragDropDataTransformer;
use App\OurEdu\ResourceSubjectFormats\Transformers\MatchingDataTransformer;
use App\OurEdu\ResourceSubjectFormats\Transformers\TrueFalseDataTransformer;
use App\OurEdu\ResourceSubjectFormats\Models\MultiMatching\MultiMatchingData;
use App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceData;
use App\OurEdu\ResourceSubjectFormats\Transformers\MultiMatchingDataTransformer;
use App\OurEdu\ResourceSubjectFormats\Transformers\MultipleChoiceDataTransformer;

class ResourceSubjectFormatSubjectTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'questionResourceSubjectFormatSubjectData'
    ];

    protected array $availableIncludes = [
    ];

    private $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    /**
     * @param ResourceSubjectFormatSubject $resourceSubjectFormatSubject
     * @return array
     */
    public function transform(ResourceSubjectFormatSubject $resourceSubjectFormatSubject)
    {
        return [
            'id' => (int)$resourceSubjectFormatSubject->id,
            'resource_id' => (string) $resourceSubjectFormatSubject->resource ? $resourceSubjectFormatSubject->resource->id : null,
            'slug' => (string) $resourceSubjectFormatSubject->resource ?  $resourceSubjectFormatSubject->resource->slug : null,
            'is_active' => (bool)$resourceSubjectFormatSubject->is_active,
            'is_editable' => (bool)$resourceSubjectFormatSubject->is_editable,
            'list_order_key' => $resourceSubjectFormatSubject->list_order_key,

        ];
    }

    public function includeQuestionResourceSubjectFormatSubjectData(ResourceSubjectFormatSubject $resourceSubjectFormatSubject)
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
}
