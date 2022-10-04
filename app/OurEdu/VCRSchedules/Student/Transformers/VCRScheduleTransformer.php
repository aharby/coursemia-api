<?php

namespace App\OurEdu\VCRSchedules\Student\Transformers;

use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Users\Models\Instructor;
use App\OurEdu\VCRSchedules\Instructor\Transformers\UserTransformer;
use App\OurEdu\VCRSchedules\Models\VCRSchedule;
use League\Fractal\TransformerAbstract;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\Users\Transformers\InstructorTransformer;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\GeneralExams\Student\Transformers\SubjectTransformer;

class VCRScheduleTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [
        'actions',
        'subject',
        'instructor',
    ];

    public function __construct(private ?Exam $exam = null)
    {
    }

    public function transform(VCRSchedule $VCRSchudle)
    {
        $user = $VCRSchudle->instructor;

        $transformerData = [
            'id' => (int) $VCRSchudle->id,
            'price' =>  (string)$VCRSchudle->price . ' ' . trans('subject_packages.riyal'),
            'price_amount' =>  (int)$VCRSchudle->price,
            'instructor_id' =>  (int)$user->id,
            'instructor_name' => (string) $user->name,
            'profile_picture' => (string) imageProfileApi($user->profile_picture),
            'rating'=> (float) $user->avgRating
        ];

        return $transformerData;
    }

    public function includeSubject(VCRSchedule $VCRSchudle)
    {
        $subject = $VCRSchudle->subject;
        if ($subject) {
            return $this->item($subject, new SubjectTransformer(), ResourceTypesEnums::SUBJECT);
        }
    }

    public function includeInstructor(VCRSchedule $VCRSchudle)
    {

      return $this->item($VCRSchudle->instructor, new UserTransformer(), ResourceTypesEnums::INSTRUCTOR);
    }


    public function includeActions(VCRSchedule $VCRSchudle)
    {
        $actions = [];
        $day = date('l', strtotime(now()));
  $actions[] = [
            'endpoint_url' => buildScopeRoute('api.student.vcr.new.post.request', ['vcr' => $VCRSchudle->id, 'day' => $day, 'exam' => $this->exam->id ?? $VCRSchudle->exam_id]),
            'label' => trans('exam.request a virtual class room'),
            'method' => 'POST',
            'key' => APIActionsEnums::NEW_REQUEST_VCR_SESSION
        ];

        return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
    }
}
