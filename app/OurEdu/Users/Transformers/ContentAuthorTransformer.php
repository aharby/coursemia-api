<?php

namespace App\OurEdu\Users\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\Users\UserEnums;
use League\Fractal\TransformerAbstract;
use App\OurEdu\Users\Models\ContentAuthor;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Users\Transformers\UserTransformer;
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
