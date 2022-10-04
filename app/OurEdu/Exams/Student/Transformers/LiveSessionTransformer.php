<?php


namespace App\OurEdu\Exams\Student\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Users\Transformers\UserTransformer;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class LiveSessionTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions',
        'subject',
        'instructor',
    ];
    protected array $availableIncludes = [

    ];
    private $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform($liveSession)
    {
        $transformerData = [
            'id' =>  Str::uuid(),
            'price' =>  $liveSession->price.' '.trans('subject_packages.riyal'),
            'instructor_id' => $liveSession->instructor_id,
            'subject_id' => $liveSession->subject_id,
        ];

        return $transformerData;
    }

    public function includeInstructor($liveSession)
    {
        if ($liveSession->instructor) {
            return $this->item($liveSession->instructor->user, new UserTransformer(), ResourceTypesEnums::USER);
        }
    }

    public function includeSubject($liveSession)
    {
        if ($liveSession->subject) {
            return $this->item($liveSession->subject, new SubjectTransformer(), ResourceTypesEnums::SUBJECT);
        }
    }

    public function includeActions($liveSession)
    {
        $day = date('l', strtotime(now()));
     $actions[] = [
            'endpoint_url' => buildScopeRoute('api.student.vcr.new.post.request', ['vcr' => $liveSession->id , 'day' => $day , 'exam' => $liveSession->exam_id]),
            'label' => trans('exam.request a virtual class room'),
            'method' => 'POST',
            'key' => APIActionsEnums::NEW_VCR_REQUEST
        ];

        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.show-instructor', ['id' => $liveSession->instructor->user->id]),
            'label' => trans('app.Instructor'),
            'method' => 'GET',
            'key' => APIActionsEnums::VIEW_INSTRUCTOR_PROFILE
        ];
        return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
    }
}
