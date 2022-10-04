<?php

namespace App\OurEdu\VCRSessions\Instructor\Transformers;


use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Users\UserEnums;
use League\Fractal\TransformerAbstract;

class ListVCRSessionParticipantsTransformer extends TransformerAbstract
{
    protected $params;

    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [
    ];

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform($participant)
    {
        if ($participant->user->type == UserEnums::STUDENT_TYPE) {
            $studentUser = $participant->user;
            $exams = Exam::where('vcr_session_id', $participant->vcr_session_id)
                ->where('student_id', $participant->user_id)
                ->where('is_finished', 1)
                ->pluck('title', 'id')
                ->toArray() ?? [];

            $transformerData = [
                'id' => $studentUser->id,
                'name' => $studentUser->name,
                'profile_picture' => imageProfileApi($studentUser->profile_picture,'small'),
                'type' => $studentUser->type,
                'exams' => (array) $exams
            ];
        }
        return $transformerData;
    }

}
