<?php


namespace App\OurEdu\SchoolAccounts\ClassroomClassSessions\SchoolSupervisor\Transformers;

use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\ClassroomClassSession;
use App\OurEdu\SchoolAccounts\ClassroomClass\SchoolBranchSupervisor\Transformers\ClassroomTransformer;
use App\OurEdu\SchoolAccounts\ClassroomClass\SchoolBranchSupervisor\Transformers\SubjectSupervisorTransformer;
use League\Fractal\TransformerAbstract;

class ClassroomClassSessionsTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];
    protected array $availableIncludes = [
        'subject' ,
        'classroom',
        'instructor',
    ];


    /**
     * @param ClassroomClassSession $session
     * @return array
     */
    public function transform(ClassroomClassSession $session)
    {
        return [
            'id' => (int)$session->id,
            'classroom_id' => (int)$session->classroom_id,
            'subject_id'=> (int)$session->subject_id,
            "day" => $session->from->format('Y-m-d'),
            "from_time" => $session->from->format("H:i"),
            "to_time" => $session->to->format("H:i"),
            "name" => $session->classroom->name . " "
                . $session->from->format('Y-m-d')
                . " (" . $session->from->format("H:i") . "-" . $session->to->format("H:i") . ")",
        ];
    }

    public function includeSubject($session) {

        if ($session->subject) {
            return $this->item($session->subject , new SubjectSupervisorTransformer() , ResourceTypesEnums::SUBJECT);
        }
    }

    public function includeClassroom($session) {

        if ($session->classroom) {
            return $this->item($session->classroom , new ClassroomTransformer() , ResourceTypesEnums::CLASSROOM);
        }
    }
    public function includeInstructor($session) {

        if ($session->instructor) {
            return $this->item($session->instructor , new InstructorTransformer() , ResourceTypesEnums::SCHOOL_INSTRUCTOR);
        }
    }
}
