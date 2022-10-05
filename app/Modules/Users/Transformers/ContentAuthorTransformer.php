<?php

namespace App\Modules\Users\Transformers;

use App\Modules\BaseApp\Api\Enums\APIActionsEnums;
use App\Modules\BaseApp\Api\Transformers\ActionTransformer;
use App\Modules\Users\UserEnums;
use League\Fractal\TransformerAbstract;
use App\Modules\Users\Models\ContentAuthor;
use App\Modules\BaseApp\Enums\ResourceTypesEnums;
use App\Modules\Users\Transformers\UserTransformer;
use App\Subjects\SME\Transformers\TaskListTransformer;

class ContentAuthorTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions'
    ];

    protected array $availableIncludes = [
        'user',
        'tasks',
    ];

    private $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform(ContentAuthor $author)
    {
        $transformedData = [
            'id' => (int) $author->id,
            'hire_date' => (string) $author->hire_date,
            'user_id' => (int) $author->user_id,
        ];

        if (isset($this->params['performance_report'])) {
            $transformedData['tasks_count'] = (int) $author->tasks_count;
            $transformedData['done_tasks_count'] = (int) $author->done_tasks_count;
            $transformedData['expired_tasks_count'] = (int) $author->expired_tasks_count;
            $transformedData['in_progress_tasks_count'] = (int) $author->in_progress_tasks_count;
        }

        return $transformedData;
    }

    public function includeUser($author)
    {
        return $this->item($author->user, new UserTransformer(), ResourceTypesEnums::USER);
    }


    public function includeTasks($author)
    {
        if ($author->tasks->count()) {
            return $this->collection($author->tasks, new TaskListTransformer(), ResourceTypesEnums::TASK);
        }
    }

    public function includeActions($author)
    {

        $actions = [];
        if (isset($this->params['performance_report'])) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.sme.subjects.get.performance.list', ['contentAuthor' => $author->user->id]),
                'label' => trans('profile.View Tasks'),
                'method' => 'GET',
                'key' => APIActionsEnums::LIST_TASKS
            ];
        }

        if (count($actions) > 0) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }
}
