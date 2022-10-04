<?php


namespace App\OurEdu\SchoolAccounts\ClassroomClassSessions\Repositories;


use App\OurEdu\SchoolAccounts\Classroom;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\ClassroomClassSession;
use App\OurEdu\BaseApp\Traits\Filterable;
use App\OurEdu\VCRSchedules\Models\VCRSession;

class ClassroomClassSessionRepository implements ClassroomClassSessionRepositoryInterface
{
    use Filterable;

    protected $session;

    public function __construct(ClassroomClassSession $session)
    {
        $this->model = $session;
    }
    public function getClassroomSessions(Classroom $classroom)
    {
        return  $sessions = ClassroomClassSession::query()
            ->where('classroom_id' , "=", $classroom->id)
            ->get();
    }

    public function getSessions($filters){
        return VCRSession::where('classroom_id',$filters['classroom_id'])
        ->whereHas('classroom',function ($query) use ($filters){
            $query->where('branch_id',$filters['branch_id']);
        })->with([
            'classroom','classroomClassSession','instructor',
            'subject.educationalSystem','subject.academicalYears','subject.gradeClass'
        ])->where('subject_id',$filters['subject_id'])->get();
    }

       public function findOrFail(int $id): ? ClassroomClassSession
    {
        return $this->model->with('vcrSession')->findOrFail($id);
    }
}
