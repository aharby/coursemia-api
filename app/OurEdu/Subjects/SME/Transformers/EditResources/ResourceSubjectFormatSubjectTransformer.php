<?php


namespace App\OurEdu\Subjects\SME\Transformers\EditResources;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\Subjects\SME\Transformers\EditResources\Resources\AudioTransformer;
use App\OurEdu\Subjects\SME\Transformers\EditResources\Resources\FlashTransformer;
use App\OurEdu\Subjects\SME\Transformers\EditResources\Resources\PageTransformer;
use App\OurEdu\Subjects\SME\Transformers\EditResources\Resources\PdfTransformer;
use App\OurEdu\Subjects\SME\Transformers\EditResources\Resources\PictureTransformer;
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

class ResourceSubjectFormatSubjectTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
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
            'task_is_done' => (boolean)($resourceSubjectFormatSubject->task->is_done ?? 0),
            'list_order_key' => $resourceSubjectFormatSubject->list_order_key ,
        ];
    }

    public function includeActions(ResourceSubjectFormatSubject $resourceSubjectFormatSubject)
    {
        $actions = [];
        $actions[] = [
            'endpoint_url' =>  buildScopeRoute('api.sme.subjects.get-edit-resource', ['resourceSubjectFormatId' => $resourceSubjectFormatSubject->id]),
            'label' => trans('app.Edit'),
            'method' => 'GET',
            'key' => APIActionsEnums::EDIT_RESOURCE
        ];

        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.sme.subjects.pause-unpause.resource',
                ['resourceId' => $resourceSubjectFormatSubject->id]),
            'label' => $resourceSubjectFormatSubject->is_active ? trans('api.pause') : trans('api.un pause'),
            'method' => 'POST',
            'key' => $resourceSubjectFormatSubject->is_active ? APIActionsEnums::PAUSE : APIActionsEnums::UN_PAUSE
        ];
        return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
    }
}
