<?php

namespace App\OurEdu\Subjects\SME\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use App\OurEdu\Subjects\Models\SubModels\Task;
use App\OurEdu\Users\Transformers\ContentAuthorTransformer;
use App\OurEdu\Users\UserEnums;
use League\Fractal\ParamBag;
use League\Fractal\TransformerAbstract;

class TestTasksTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [
        'contentAuthor',
    ];


    public function transform(Task $task)
    {
        $transformedData = [
            'id' => (int)$task->id,
            'title' => (string)$task->title,
            'is_active' => (bool)$task->is_active,
            'is_expired' => (bool)$task->is_expired,
            'is_done' => (bool)$task->is_done,
            'is_assigned' => (bool)$task->is_assigned,
            'due_date' => (int)$task->due_date,
            'created_at' => (string)$task->created_at,

            'subject_id' => (string)$task->subject_id,
            'resource_subject_format_subject_id' => (string)$task->resource_subject_format_subject_id,
            'subject_format_subject_id' => (string)$task->subject_format_subject_id,
        ];
        if ($task->contentAuthors()->exists()) {
            $transformedData['last_update_date'] = (string) $task->contentAuthors()->latest()->first()->created_at;
        }
        return $transformedData;
    }

    public function includeContentAuthor(Task $task)
    {
        $contentAuthor = $task->contentAuthors()->latest()->first();
        if ($contentAuthor) {
            return $this->item($contentAuthor, new TaskContentAuthorTransformer(), ResourceTypesEnums::CONTENT_AUTHOR);
        }
    }
}
