<?php

namespace App\OurEdu\LearningPerformance\Parent\Transformers\ListsTransformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\Models\Student;
use League\Fractal\TransformerAbstract;

class SubjectsListTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions'
    ];

    private $student;

    public function __construct(Student $student)
    {
        $this->student = $student;
    }

    public function transform(Subject $subject)
    {
        $progress = calculateSubjectProgress($subject, $this->student->user);
        $currencyCode = $this->student->educationalSystem->country->currency ?? '';

        return [
            'id' => (int)$subject->id,
            'name' => (string)$subject->name,
            'section_type' => is_null($subject->section_type) ? 'section' : $subject->section_type,
            'subject_image' => imageProfileApi($subject->image),
            'color' => (string)$subject->color,
            'subscription_cost' => $subject->subscription_cost . " " . $currencyCode,
            'progress' => round($progress),
            'time' => getStudentSubjectTimeInHours($subject, $this->student) . " " . trans('subject.Hours'),
            'is_subscribe' => is_student_subscribed($subject, $this->student->user),
            'apple_price' => $subject->apple_price. " " . $currencyCode
        ];
    }

    public function includeActions($subject)
    {
        $actions = [];

        if (!is_student_subscribed($subject, $this->student->user)) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute(
                    'api.parent.subscriptions.post.subjectSubscripe',
                    [
                        'id' => $subject->id,
                        'studentId' => $this->student->id
                    ]
                ),
                'label' => trans('app.Subscribe'),
                'method' => 'POST',
                'key' => APIActionsEnums::SUBJECT_SUBSCRIBE
            ];
        } else {
            $actions[] = [
                'endpoint_url' => buildScopeRoute(
                    'api.parent.learningPerformance.get.studentSubjectPerformance',
                    [
                        'studentId' => $this->student->id,
                        'subjectId' => $subject->id
                    ]
                ),
                'label' => trans('app.Performance'),
                'method' => 'GET',
                'key' => APIActionsEnums::LEARNING_PERFORMANCE
            ];
        }

        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }
}
