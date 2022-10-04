<?php


namespace App\OurEdu\VCRSchedules\Instructor\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\VCRSchedules\VCRRequestStatusEnum;
use League\Fractal\TransformerAbstract;

class VCRRequestsTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions' ,
    ];

    protected array $availableIncludes = [
        'student' ,
    ];

    public function __construct()
    {
    }

    public function transform($VCRRequest)
    {
        $transformerData = [
            'id' => $VCRRequest->id,
            'price' => $VCRRequest->price.' '.trans('subject_packages.riyal'),
            'status' => $VCRRequest->status
        ];

        return $transformerData;
    }

    public function includeStudent($VCRRequest)
    {
        $student =  $VCRRequest->student;
        return $this->collection($student, new UserTransformer(), ResourceTypesEnums::USER);
    }

    public function includeActions($request)
    {
        //view exam report
        $actions = [];

        if ($request->exam_id) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.instructor.vcr.getStudentReport', ['requestId' => $request->id]),
                'label' => trans('vcr.View Student Exam Report'),
                'key' => APIActionsEnums::VIEW_STUDENT_EXAM_REPORT,
                'method' => 'GET'
            ];
        }
        if ($request->status != VCRRequestStatusEnum::ACCEPTED) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.instructor.vcr.acceptVcrRequest', ['requestId' => $request->id]),
                'label' => trans('vcr.Accept Request'),
                'key' => APIActionsEnums::ACCEPT_VCR_REQUEST,
                'method' => 'POST'
            ];
        }

        if (count($actions))
        return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
    }
}
