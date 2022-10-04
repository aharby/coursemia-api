<?php


namespace App\OurEdu\LearningPerformance\StudentTeacher\Transformers;

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
    private $isSubscribe;

    public function __construct(Student $student)
    {
        $this->student = $student;
    }

    public function transform(Subject $subject)
    {
        $progress = calculateSubjectProgress($subject, $this->student->user);
        $this->isSubscribe = is_student_subscribed($subject , $this->student->user);
        return [
            'id' => (int)$subject->id,
            'name' => (string)$subject->name,
            'section_type' => is_null($subject->section_type) ? 'section' : $subject->section_type,
            'subject_image' => (string) imageProfileApi($subject->image),
            'color' => (string)$subject->color,
            'progress' => round($progress),
            'is_subscribe' => $this->isSubscribe,
        ];
    }

    public function includeActions($subject)
    {
        $actions = [];

        if ($this->isSubscribe) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.parent.learningPerformance.get.studentSubjectPerformance',
                    ['studentId'=>$this->student->id,
                        'subjectId'=> $subject->id]),
                'label' => trans('app.Performance'),
                'method' => 'GET',
                'key' => APIActionsEnums::LEARNING_PERFORMANCE
            ];

            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.student.subjects.view-subject',
                    ['subjectId' => $subject->id, 'studentId' => $this->student->id]),
                'label' => trans('subjects.View subject'),
                'method' => 'GET',
                'key' => APIActionsEnums::VIEW_SUBJECT
            ];
        }

        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }
}

