<?php


namespace App\OurEdu\SchoolAccounts\SessionPreparations\SchoolInstructor\Transformers;


use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;

use App\OurEdu\SchoolAccounts\Classroom;
use App\OurEdu\SchoolAccounts\Classroom\EducationalSupervisor\Transformars\ClassroomTransform;
use App\OurEdu\SchoolAccounts\ClassroomClass\ClassroomClass;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\ClassroomClassSession;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\SchoolSupervisor\Transformers\ClassroomClassSessionsTransformer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;


class LookUpTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'classrooms',
        'sessions',
    ];
    protected array $availableIncludes = [
    ];

    private $param;
    private $user;
    private $session_id;
    private $session;

    public function __construct(array $param, $session_id)
    {
        $this->param = $param;
        $this->user = Auth::guard('api')->user();
        $this->session_id = $session_id;
    }

    /**
     * @return array
     */
    public function transform()
    {
        return [
            'id' => Str::uuid()
        ];
    }

    private function getClassRoomClassIds(array $classroomIds = [])
    {
        $this->session = ClassroomClassSession::where('instructor_id', $this->user->id)->findOrFail($this->session_id);
        $classroomClassesQuery = ClassroomClass::where("instructor_id", $this->user->id)
            ->where('subject_id', $this->session->subject_id)
            ->whereHas('sessions', function ($q) {
                $q->where('from', '>=', Carbon::parse($this->session->from));
                $q->where('from', '<=', Carbon::parse($this->session->from)->addWeek(2)->format('Y-m-d'));
                $q->doesntHave('preparation');
            });

        if (count($classroomIds)) {
            $classroomClassesQuery->whereHas('classroom', function ($q) use ($classroomIds) {
                $q->whereIn('id', $classroomIds);
            });
        }

        return $classroomClassesQuery->pluck('id');
    }

    public function includeClassRooms()
    {
        $classroomClassesIds = $this->getClassRoomClassIds();
        $classrooms = Classroom::whereHas('classroomClass', function ($q) use ($classroomClassesIds) {
            $q->whereIn('id', $classroomClassesIds);
        })
            ->get();
        if ($classrooms) {
            return $this->collection($classrooms, new ClassroomTransform(), ResourceTypesEnums::CLASSROOM);
        }
    }

    public function includeSessions()
    {
        $classroomClassesIds = $this->getClassRoomClassIds($this->param['classroom_id'] ?? []);
        $sessions = ClassroomClassSession::whereHas('classroomClass', function ($q) use ($classroomClassesIds) {
            $q->whereIn('id', $classroomClassesIds);
        })
            ->where('instructor_id', $this->user->id)
            ->where('from', '>=', Carbon::parse($this->session->from))
            ->where('from', '<=', Carbon::parse($this->session->from)->addWeek(2)->format('Y-m-d'))
            ->doesntHave('preparation')
            ->get();
        if ($sessions) {
            return $this->collection($sessions, new ClassroomClassSessionsTransformer(), ResourceTypesEnums::CLASSROOM_CLASS_SESSION);
        }
    }
}
