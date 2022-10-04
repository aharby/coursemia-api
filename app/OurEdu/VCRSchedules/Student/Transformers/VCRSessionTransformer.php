<?php

namespace App\OurEdu\VCRSchedules\Student\Transformers;

use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\Ratings\Transformers\RatingTransformer;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\VCRSchedules\Instructor\Transformers\StudentTransformer;

class VCRSessionTransformer extends TransformerAbstract
{
    protected $params;

    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [
        'actions',
        'student',
        'ratings',
    ];

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform($vCRSession)
    {
        $user = Auth::guard('api')->user();

        $transformerData = [
            'id' => $vCRSession->id,
            'price' => $vCRSession->price.' '.trans('subject_packages.riyal'),
            'status' => $vCRSession->status,
            'student_id' => $vCRSession->student_id,
            'instructor_id' => $vCRSession->instructor_id,
            'subject_id' => $vCRSession->subject_id,
            'vcr_request_id' => $vCRSession->vcr_request_id,
            'join_url' => $vCRSession->student_join_url,
            'ended_at' => $vCRSession->ended_at,
            'user' => [
                'type' => $user->type,
                'name' => $user->name,
                'rolte'=>($user->type=='student')

            ]
        ];

        return $transformerData;
    }

    public function includeStudent($vCRSession)
    {
        $student = $vCRSession->student;
        if ($student) {
            return $this->collection($student, new StudentTransformer(), ResourceTypesEnums::STUDENT);
        }
    }

    public function includeActions($vCRSession)
    {
        $actions = [];

        //view exam report
        if ($vCRSession->exam_id) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.instructor.vcr.getStudentReport', ['studentID' => $vCRSession->student_id, 'examId' => $vCRSession->exam_id]),
                'label' => trans('vcr.View Student Exam Report'),
                'key' => APIActionsEnums::VIEW_STUDENT_EXAM_REPORT,
                'method' => 'GET'
            ];
        }

        return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
    }

    public function includeRatings($vCRSession)
    {
        if ($vCRSession->ratings->count()) {
            return $this->collection($vCRSession->ratings, new RatingTransformer(), ResourceTypesEnums::RATING);
        }
    }
}
