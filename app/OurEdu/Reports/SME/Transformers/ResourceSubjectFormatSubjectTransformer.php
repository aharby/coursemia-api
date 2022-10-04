<?php

namespace App\OurEdu\Reports\SME\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use League\Fractal\TransformerAbstract;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;

class ResourceSubjectFormatSubjectTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions'
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

    public function includeActions($resourceSubjectFormatSubject)
    {
        $actions = [];

        if ($resourceSubjectFormatSubject->reports()->count()){
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.sme.reports.list.reports', ['resource_id' => $resourceSubjectFormatSubject->id]),
                'label' => trans('subject.View Resource Reports'),
                'method' => 'GET',
                'key' => APIActionsEnums::LIST_REPORTS
            ];
        }

        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }

    }

}
