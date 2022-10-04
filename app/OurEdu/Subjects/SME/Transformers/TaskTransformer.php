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

class TaskTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'subject',
        'resourceSubjectFormatSubject',
        'subjectFormatSubject',
        'actions',
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


    public function includeActions($task)
    {
        $actions = [];
        if (auth()->user()->type == UserEnums::CONTENT_AUTHOR_TYPE) {
            if ($task->is_assigned == 0) {
                $actions[] = [
                    'endpoint_url' => buildScopeRoute('api.contentAuthor.subjects.pullTask', ['id' => $task->id]),
                    'label' => trans('task.Pull Task'),
                    'key' => APIActionsEnums::PULL_TASK,
                    'method' => 'POST'
                ];
            }

            if ($task->is_assigned == 1) {
                $actions[] = [
                    'endpoint_url' => buildScopeRoute('api.contentAuthor.subjects.getFillResource', ['resourceId' => $task->resourceSubjectFormatSubject->id]),
                    'label' => trans('task.Fill Task'),
                    'key' => APIActionsEnums::FILL_RESOURCE,
                    'method' => 'GET'
                ];
            }
        }
        if (count($actions) > 0) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }


    public function includeSubject(Task $task)
    {
        $subject = $task->subject;

        if ($subject) {
            return $this->item($subject, new SubjectTransformer(), ResourceTypesEnums::SUBJECT);
        }
    }

    public function includeResourceSubjectFormatSubject(Task $task)
    {
        $resourceSubjectFormatSubject = $task->resourceSubjectFormatSubject;

        if ($resourceSubjectFormatSubject) {
            return $this->item($resourceSubjectFormatSubject, new ResourceSubjectFormatSubjectTransformer(), ResourceTypesEnums::RESOURCE_SUBJECT_FORMAT_SUBJECT);
        }
    }

    public function includeSubjectFormatSubject(Task $task)
    {
        $subjectFormatSubject = $task->subjectFormatSubject;
        if ($subjectFormatSubject) {
            return $this->item($subjectFormatSubject, new SubjectFormatSubjectTransformer(), ResourceTypesEnums::SUBJECT_FORMAT_SUBJECT);
        }
    }

    public function includeContentAuthor(Task $task)
    {
        $contentAuthor = $task->contentAuthors()->latest()->first();
        if ($contentAuthor) {
            return $this->item($contentAuthor, new TaskContentAuthorTransformer(), ResourceTypesEnums::CONTENT_AUTHOR);
        }
    }
}
