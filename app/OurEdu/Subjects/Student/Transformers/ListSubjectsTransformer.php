<?php


namespace App\OurEdu\Subjects\Student\Transformers;

use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\Reports\ReportEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\OurEdu\Subjects\Models\Subject;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;

class ListSubjectsTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions',
    ];

    protected array $availableIncludes = [
    ];

    private $params;
    private $user;

    public function __construct($params = [], $user = null)
    {
        $this->params = $params;
        $this->user = $user ?? new User;
    }

    /**
     * @param Subject $subject
     * @return       array
     */

    public function transform(Subject $subject)
    {
        $progress = calculateSubjectProgress($subject,$this->user);
        $curencyCode = $subject->educationalSystem->country->currency ?? '';

        // to view the subject inside a package
        if (isset($this->params['view_inside_package'])) {
            return [
                'id' => (int)$subject->id,
                'name' => (string)$subject->name,
                'truncate_name' => (string)(truncateString($subject->name)),
                'subject_image' => (string) imageProfileApi($subject->image, 'small'),
                'color' => (string)$subject->color,
                'section_type' => is_null($subject->section_type) ? 'section' : $subject->section_type,
            ];
        }
        return [
            'id' => (int)$subject->id,
            'name' => (string)$subject->name,
            'truncate_name' => (string)(truncateString($subject->name,10)),

            'section_type' => is_null($subject->section_type) ? 'section' : $subject->section_type,
            'subject_image' => (string) imageProfileApi($subject->image),
            'color' => (string)$subject->color,
            'progress' => round($progress),
            'is_subscribe' => is_student_subscribed($subject , $this->user),
            'subscription_cost' => $subject->subscription_cost . " " . $curencyCode,
            'apple_price' => $subject->apple_price. " " . $curencyCode
        ];
    }

    public function includeActions($subject)
    {
        $actions = [];

        if ($authUser = Auth::guard('api')->user()) {

            // parent case
            if ($authUser->type == UserEnums::PARENT_TYPE && $student = $this->user->student) {
                $userIsSubscriped = DB::table('subject_subscribe_students')
                    ->where('subject_id', $subject->id)
                    ->where('student_id', $student->id)
                    ->exists();

                if (! $userIsSubscriped) {
                    $actions[] = [
                        'endpoint_url' => buildScopeRoute('api.parent.subscriptions.post.subjectSubscripe', ['id' => $subject->id,'studentId'=>$student->id]),
                        'label' => trans('app.Subscribe'),
                        'method' => 'POST',
                        'key' => APIActionsEnums::SUBJECT_SUBSCRIBE
                    ];
                }

                if (! $userIsSubscriped && $student->wallet_amount < $subject->subscription_cost) {
                    $actions[] = [
                        'endpoint_url' => buildScopeRoute('api.parent.payments.submitTransaction', ['student_id' => $this->user->student->id]),
                        'label' => trans('subscriptions.Add money'),
                        'method' => 'POST',
                        'key' => APIActionsEnums::ADD_MONEY_TO_WALLET
                        ];
                }
            }


            // student case
            if ($authUser->type == UserEnums::STUDENT_TYPE && $student = $authUser->student) {
                $userIsSubscriped = DB::table('subject_subscribe_students')
                    ->where('subject_id', $subject->id)
                    ->where('student_id', $student->id)
                    ->exists();

                if (! $userIsSubscriped && ! isset($this->params['view_inside_package'])) {
                    $actions[] = [
                        'endpoint_url' => buildScopeRoute('api.student.subjects.post.subscribe', ['subjectId' => $subject->id]),
                        'label' => trans('app.Subscribe'),
                        'method' => 'POST',
                        'key' => APIActionsEnums::SUBJECT_SUBSCRIBE
                    ];

                }
                if ($userIsSubscriped){
                    $actions[] = [
                        'endpoint_url' => buildScopeRoute('api.student.report.post.create', ['subjectId' => $subject->id,'reportType'=>ReportEnum::SUBJECT_TYPE,'id'=>$subject->id]),
                        'label' => trans('subject.Report'),
                        'method' => 'POST',
                        'key' => APIActionsEnums::REPORT
                    ];
                }
            }

            if ($this->user->type == UserEnums::STUDENT_TYPE)
            {
                $actions[] = [
                    'endpoint_url' => buildScopeRoute('api.student.subjects.view-subject', [
                        'subjectId' => $subject->id,
                        'studentId' => $this->user->student->id
                    ]),
                    'label' => trans('subjects.View subject'),
                    'method' => 'GET',
                    'key' => APIActionsEnums::VIEW_SUBJECT
                ];
                return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
            }

            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.student.subjects.view-subject', ['subjectId' => $subject->id]),
                'label' => trans('subjects.View subject'),
                'method' => 'GET',
                'key' => APIActionsEnums::VIEW_SUBJECT
            ];

            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }
}
