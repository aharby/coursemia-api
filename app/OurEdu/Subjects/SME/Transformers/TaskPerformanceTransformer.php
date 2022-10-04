<?php

namespace App\OurEdu\Subjects\SME\Transformers;

use League\Fractal\TransformerAbstract;
use App\OurEdu\Users\Models\ContentAuthor;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\Users\Transformers\UserTransformer;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;

class TaskPerformanceTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [
        'actions',
        'user'
    ];

    private $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform(ContentAuthor $author)
    {
        $transformedData['id'] = (int) $author->id;

        $transformedData['tasks_count'] = (int) $author->tasks_count;
        $transformedData['done_tasks_count'] = (int) $author->done_tasks_count;
        $transformedData['expired_tasks_count'] = (int) $author->expired_tasks_count;
        $transformedData['in_progress_tasks_count'] = (int) $author->in_progress_tasks_count;

        return $transformedData;
    }

    public function includeActions($author)
    {
        $actions = [];

        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.sme.subjects.get.performance.list', ['contentAuthor' => $author->user->id]),
            'label' => trans('profile.View Tasks'),
            'method' => 'GET',
            'key' => APIActionsEnums::LIST_TASKS
        ];

        return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
    }

    public function includeUser($author)
    {
        if ($author->user) {
            return $this->item($author->user, new UserTransformer(), ResourceTypesEnums::USER);
        }
    }
}
