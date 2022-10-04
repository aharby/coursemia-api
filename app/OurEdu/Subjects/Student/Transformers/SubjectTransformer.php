<?php

namespace App\OurEdu\Subjects\Student\Transformers;

use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\Reports\ReportEnum;
use App\OurEdu\GarbageMedia\MediaEnums;
use App\OurEdu\Subjects\Models\Subject;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;

class SubjectTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions'
    ];
    protected array $availableIncludes = [
        'actions',
        'subjectMediaTypes',
        'subjectFormatSubjects',
        'sections'
    ];
    private $params;
    private $user;

    public function __construct($params = [], $user = null)
    {
        $this->params = $params;
        $this->user = $user ?? new User();
    }

    /**
     * @param Subject $subject
     * @return array
     */
    public function transform(Subject $subject)
    {
        $progress = calculateSubjectProgress($subject, $this->user);
        $currencyCode = $this->user->student->educationalSystem->country->currency ?? '';
        $transformedData = [
            'id' => (int)$subject->id,
            'name' => (string)$subject->name,
            'educational_system' => (string)($subject->educationalSystem->name ?? ''),
            'academical_years' => (string)($subject->academicalYears->title ?? ''),
            'grade_class' => (string)($subject->gradeClass->title ?? ''),
            'subscription_cost' => $subject->subscription_cost . " " . $currencyCode,
            'subject_image' => imageProfileApi($subject->image, 'large'),
            'color' => (string)$subject->color,
            'subject_library_text' => $subject->subject_library_text,
            'is_subscribe' => is_student_subscribed($subject, $this->user),
            'direction' => $subject->direction,
            'subscription_amount' => $subject->subscription_cost,
            'apple_price' => $subject->apple_price. " " . $currencyCode
        ];
        if (is_student_subscribed($subject, $this->user)) {
            $transformedData['progress'] = round($progress);
        }
        return $transformedData;
    }

    /**
     * @param $subject
     * @return Collection
     */
    public function includeActions($subject)
    {
        $actions = [];
        if (isset($this->params['view_subject_sections'])) {
            return;
        }
        if (auth()->user()->type == UserEnums::STUDENT_TYPE) {
            if (
                !$subject->students()
                    ->where('subject_subscribe_students.student_id', auth()->user()->student->id)
                    ->exists()
            ) {
                // Not Subscribed actions
                $actions[] = [
                    'endpoint_url' => buildScopeRoute(
                        'api.student.subjects.post.subscribe',
                        ['subjectId' => $subject->id]
                    ),
                    'label' => trans('subject.Subscribe'),
                    'method' => 'POST',
                    'key' => APIActionsEnums::SUBJECT_SUBSCRIBE
                ];
            } else {
                //  Subscribed actions
                $actions[] = [
                    'endpoint_url' => buildScopeRoute(
                        'api.student.report.post.create',
                        ['subjectId' => $subject->id, 'reportType' => ReportEnum::SUBJECT_TYPE, 'id' => $subject->id]
                    ),
                    'label' => trans('subject.Report'),
                    'method' => 'POST',
                    'key' => APIActionsEnums::REPORT
                ];
            }

            if (isset($this->params['show_details'])) {
                $actions[] = [
                    'endpoint_url' => buildScopeRoute(
                        'api.student.subjects.view-subject',
                        ['subjectId' => $subject->id]
                    ),
                    'label' => trans('subject.View Subject'),
                    'method' => 'GET',
                    'key' => APIActionsEnums::VIEW_SUBJECT
                ];
            }
        }
        if (auth()->user()->type == UserEnums::PARENT_TYPE && $student = $this->user->student) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.parent.learningPerformance.get.studentSubjectPerformance', [
                    'studentId' => $student->id,
                    'subjectId' => $subject->id
                ]),
                'label' => trans('subject.View Subject Log'),
                'method' => 'GET',
                'key' => APIActionsEnums::VIEW_SUBJECT_LOG
            ];
        }

        if (auth()->user()->type == UserEnums::STUDENT_TEACHER_TYPE && $student = $this->user->student) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute(
                    'api.studentTeacher.learningPerformance.get.studentSubjectPerformance',
                    [
                        'studentId' => $student->id,
                        'subjectId' => $subject->id
                    ]
                ),
                'label' => trans('subject.View Subject Log'),
                'method' => 'GET',
                'key' => APIActionsEnums::VIEW_SUBJECT_LOG
            ];
        }

        if (!count($actions)) {
            return;
        }

        return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
    }

    public function includeSubjectMediaTypes($subject)
    {
        $subjectMedia = $subject->media()->distinct('extension')->select('extension', 'subject_id')->get();

        $mediaTypes = collect(MediaEnums::getTypeExtensions());

        $availableTypes = collect([]);

        $mediaTypes->each(function ($extensions, $type) use ($subjectMedia, $availableTypes) {
            if ($mediaFile = $subjectMedia->whereIn('extension', $extensions)->first()) {
                $availableTypes->push([
                    'subject_id' => $mediaFile->subject_id,
                    'name' => trans("media.{$type}"),
                    'type' => $type,
                    'media_type' => $type,
                ]);
            }
        });

        if ($availableTypes->count()) {
            return $this->collection(
                $availableTypes,
                new SubjectMediaTypesTransformer(),
                ResourceTypesEnums::SUBJECT_MEDIA_TYPE
            );
        }
    }


    public function includeSubjectFormatSubjects(Subject $subject)
    {
        $subjectFormatSubjects = $subject->subjectFormatSubject()
            ->doesntHave('activeReportTasks')->doesntHave('activeTasks')
            ->whereNull('parent_subject_format_id');
//            ->where('has_data_resources' , true);

        $subjectFormatSubjectsData = $subjectFormatSubjects->orderBy('list_order_key', 'ASC')->get();

        if (count($subjectFormatSubjectsData)) {
            $this->params['studentUser'] = $this->user;
            return $this->collection(
                $subjectFormatSubjectsData,
                new SubjectFormatSubjectTransformer($this->params),
                ResourceTypesEnums::SUBJECT_FORMAT_SUBJECT
            );
        }
    }

    public function includeSections(Subject $subject)
    {
        $sections = $subject->subjectFormatSubject()
            ->doesntHave('activeReportTasks')
            ->doesntHave('activeTasks')
            ->where('parent_subject_format_id', null)
            ->orderBy('list_order_key', 'ASC')
            ->get();

        if (count($sections)) {
            return $this->collection(
                $sections,
                new SectionTransformer($this->params),
                ResourceTypesEnums::SUBJECT_FORMAT_SUBJECT
            );
        }
    }
}
