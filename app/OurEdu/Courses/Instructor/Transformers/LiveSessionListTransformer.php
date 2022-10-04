<?php

namespace App\OurEdu\Courses\Instructor\Transformers;

use App\OurEdu\Users\User;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Courses\Models\SubModels\LiveSession;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\Subjects\SME\Transformers\SubjectTransformer;

class LiveSessionListTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'sessions',
        'actions',
        'subject',

    ];

    protected array $availableIncludes = [
    ];

    protected $user;

    public function __construct($user = null)
    {
        $this->user = $user ?? new User;
    }

    public function transform(LiveSession $liveSession)
    {
      $currencyCode = $this->user->student->educationalSystem->country->currency ?? '';

        $transformedData = [
            'id' => (int) $liveSession->id,
            'name' => (string) $liveSession->name,
            'instructor_id' => (int) $liveSession->instructor_id,
            'subject_id' => (int) $liveSession->subject_id,
            'subscription_cost' =>(float) $liveSession->subscription_cost . " " . $currencyCode,
            'is_active' => (boolean) $liveSession->is_active,
            'picture' => (string) imageProfileApi($liveSession->picture,'small')
        ];

        return $transformedData;
    }

    public function includeActions(LiveSession $liveSession)
    {
        $actions = [];

        if (count($actions)){
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }


    public function includeSubject(LiveSession $liveSession)
    {
        if ($liveSession->subject) {
            return $this->item($liveSession->subject, new SubjectTransformer(), ResourceTypesEnums::SUBJECT);
        }
    }

    public function includeSessions(LiveSession $liveSession)
    {
        return $this->collection($liveSession->sessions, new CourseSessionTransformer(), ResourceTypesEnums::COURSE_SESSION);
    }
}
