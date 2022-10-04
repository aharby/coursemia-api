<?php


namespace App\OurEdu\Reports\Parent\Transformers;


use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\ClassroomClassSession;
use App\OurEdu\Users\User;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSchedules\Models\VCRSessionPresence;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class StudentsAbsenceTransformer extends TransformerAbstract
{
//    protected array $defaultIncludes
    protected array $defaultIncludes = [
        "instructor", "subject"
    ];

    public function transform(ClassroomClassSession $session)
    {
        return [
            "id" => (int)$session->id,
            "classroom_class_id" => $session->classroom_class_id,
            "classroom_id" => $session->classroom_id,
            "instructor_id" => $session->instructor_id,
            "subject_id" => $session->subject_id,
            "from" => $session->from->format("y-m-d H:i"),
            "to" => $session->to->format("y-m-d H:i"),
            "from_date" => $session->from_date,
            "from_time" => $session->from_time,
            "to_date" => $session->to_date,
            "to_time" => $session->to_time,
            "vcr_id" => $session->vcrSession->id,
            "is_attend" => $session->isAttend,
            "count_of_media"=>$session->count_of_media,
            "count_of_viewed_media"=>$session->count_of_viewed_media,
            "count_of_downloaded_media"=>$session->count_of_downloaded_media,
            "hasLeft"=>$session->hasLeft,
            "left_at"=>$session->left_at
        ];
    }



    /**
     * @param ClassroomClassSession|null $session
     * @return Item
     */
    public function includeInstructor(ClassroomClassSession $session = null)
    {
        $instructor = $session->vcrSession->instructor ?? null;

        if ($instructor) {
            return $this->item($instructor, new InstructorTransformer(), ResourceTypesEnums::INSTRUCTOR);
        }
    }

    public function includeSubject(ClassroomClassSession $session)
    {
        $subject = $session->subject ?? null;

        if ($subject) {
            return $this->item($subject, new SubjectTransformer(), ResourceTypesEnums::SUBJECT);
        }
    }
}
