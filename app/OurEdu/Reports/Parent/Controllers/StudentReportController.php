<?php


namespace App\OurEdu\Reports\Parent\Controllers;


use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Reports\Parent\Requests\StudentAbsenceRequest;
use App\OurEdu\Reports\Parent\Transformers\StudentsAbsenceTransformer;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\ClassroomClassSession;
use App\OurEdu\Users\User;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSchedules\Models\VCRSessionPresence;
use Carbon\Carbon;
use Swis\JsonApi\Client\Interfaces\ParserInterface;

class StudentReportController extends BaseApiController
{
    /**
     * @var ParserInterface
     */
    private $parserInterface;

    private $filters;

    /**
     * StudentReportController constructor.
     * @param ParserInterface $parserInterface
     */
    public function __construct(ParserInterface $parserInterface)
    {
        $this->parserInterface = $parserInterface;
        $this->setFilters();
    }

    public function absence(StudentAbsenceRequest $request)
    {
        $user = User::query()->find($request->get("student_id"));
        $student = $user->student;

        $sessions = ClassroomClassSession::query()
            ->where('classroom_id' , $student->classroom_id)
            ->with("vcrSession.student")
            ->whereHas("vcrSession");

        if ($request->filled("from")) {
            $sessions = $sessions->where("from", ">=", Carbon::parse($request->get("from"))->format("Y-m-d 00:00"));
        }

        if ($request->filled("to") and Carbon::parse($request->get("to"))->format("Y-m-d") < Carbon::now()->format("Y-m-d")) {
            $sessions = $sessions->where("to", "<", Carbon::parse($request->get("to"))->addDay()->format("Y-m-d 00:00"));
        } else {
            $sessions = $sessions->where("to", "<=", Carbon::now()->format("Y-m-d H:i"));
        }

        if ($request->filled("subject_id")) {
            $sessions = $sessions->where("subject_id", "=", $request->get("subject_id"));
        }

        $sessions = $sessions->orderByDesc("from")->paginate(env('PAGE_LIMIT', 20));

        foreach ($sessions as $session) {
            $attends = $this->isAttend($session->vcrSession, $user);
            $session->isAttend = isset($attends);
            $session->hasLeft = isset($attends) && !is_null($attends->left_at)?true:false;
            $session->left_at = isset($attends) && !is_null($attends->left_at)?$attends->left_at:null;
            $preparation_media = $session->preparation && $session->preparation->media ? $session->preparation->media->pluck('id')->toArray():null;
            $session->count_of_media = $preparation_media ? count($preparation_media):0;
            $session->count_of_viewed_media = $preparation_media ?
                count(array_unique($user->preparationMedia
                ->whereIn('id',$preparation_media)
                ->whereNotNull('pivot.viewed_at')->pluck('id')->toArray())):0;
            $session->count_of_downloaded_media = $preparation_media ?
                count(array_unique($user->preparationMedia
                ->whereIn('id',$preparation_media)
                ->whereNotNull('pivot.downloaded_at')->pluck('id')->toArray())):0;
        }

        $meta = [
            'filters' => formatFiltersForApi($this->filters)
        ];

        return $this->transformDataModInclude($sessions, "", new StudentsAbsenceTransformer(), ResourceTypesEnums::STUDENT_ABSENCE_REPORT, $meta);
    }

    private function isAttend(VCRSession $session, User $user)
    {
        $absence = VCRSessionPresence::query()
            ->where("vcr_session_id", "=", $session->id)
            ->where("user_id", "=", $user->id)
            ->first();

        return $absence;
    }

    protected function setFilters()
    {
        $user = User::query()->whereHas("student")->findOrFail(request()->get('student_id'));

        $studentSubjects = $user->student->subjects()->get()->pluck('name' , 'id')->toArray();

        $this->filters[] = [
            'name' => 'subject_id',
            'type' => 'select',
            'data' => $studentSubjects,
            'trans' => false,
            'value' => request()->get('subject_id'),
        ];
    }
}
