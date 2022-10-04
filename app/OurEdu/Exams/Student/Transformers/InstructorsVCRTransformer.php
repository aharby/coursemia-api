<?php

namespace App\OurEdu\Exams\Student\Transformers;

use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Users\User;
use App\OurEdu\VCRSchedules\Student\Transformers\VCRScheduleTransformer;
use League\Fractal\TransformerAbstract;

class InstructorsVCRTransformer extends TransformerAbstract
{
    protected array $availableIncludes = [
        'vcrSpot'
    ];

    public function __construct(private Exam $exam)
    {
    }

    public function transform(User $user){

        return [
            'id'=> $user->id,
            'name' => $user->name,
            'profile_picture' => (string) imageProfileApi($user->profile_picture),
            'rating'=> $user->avgRating
        ];
    }

    public function includeVcrSpot(User $user)
    {
        $vcr = $user->vcrSchedule->first();
        if ($vcr) {
            return $this->item($vcr, new VCRScheduleTransformer($this->exam), ResourceTypesEnums::VCR_SPOT);
        }
    }
}
