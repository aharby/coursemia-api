<?php

namespace App\OurEdu\Subjects\SME\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use App\OurEdu\Subjects\Models\SubModels\Task;
use App\OurEdu\Users\UserEnums;
use League\Fractal\ParamBag;
use League\Fractal\TransformerAbstract;

class TaskListTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions',
    ];

    protected array $availableIncludes = [
        'subject',
        'resourceSubjectFormatSubject',
        'subjectFormatSubject',
    ];


    public function transform(Task $task)
    {
        return [
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

        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.sme.subjects.get.view_task', ['id' => $task->id]),
            'label' => trans('task.View Task'),
            'key' => APIActionsEnums::VIEW_TASK,
            'method' => 'GET'
        ];

        return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
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
}
