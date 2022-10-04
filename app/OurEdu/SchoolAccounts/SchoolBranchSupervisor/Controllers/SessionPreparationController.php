<?php


namespace App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\Controllers;


use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\GarbageMedia\MediaEnums;
use App\OurEdu\SchoolAccounts\ClassroomClass\Repositories\ClassroomClassRepositoryInterface;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\ClassroomClassSession;
use App\OurEdu\SchoolAccounts\Repositories\ClassroomRepositoryInterface;
use App\OurEdu\SchoolAccounts\SessionPreparations\Models\PreparationMedia;
use App\OurEdu\SchoolAccounts\SessionPreparations\Repositories\SessionPreparationRepository\SessionPreparationRepository;
use App\OurEdu\SchoolAccounts\SessionPreparations\Repositories\SessionPreparationRepository\SessionPreparationRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionPreparationController extends BaseController
{
    /**
     * @var SessionPreparationRepository
     */
    private $preparationRepository;
    /**
     * @var ClassroomRepositoryInterface
     */
    private $classroomRepository;
    /**
     * @var ClassroomClassRepositoryInterface
     */
    private $classroomClassRepository;

    /**
     * SessionPreparationController constructor.
     * @param SessionPreparationRepositoryInterface $preparationRepository
     * @param ClassroomRepositoryInterface $classroomRepository
     * @param ClassroomClassRepositoryInterface $classroomClassRepository
     */
    public function __construct(
        SessionPreparationRepositoryInterface $preparationRepository,
        ClassroomRepositoryInterface $classroomRepository,
        ClassroomClassRepositoryInterface $classroomClassRepository
    )
    {
        $this->preparationRepository = $preparationRepository;
        $this->classroomRepository = $classroomRepository;
        $this->classroomClassRepository = $classroomClassRepository;
    }

    public function getMediaLibrary(Request $request)
    {
        $data = [];
        $user = Auth::user();
        $branch = $user->branch;

        if ($request->filled("classroom")) {
            $classSessions = $this->classroomClassRepository->getByClassroom($this->classroomRepository->find($request->get("classroom")));
            $prepareClassSessions = [];
            foreach ($classSessions as $session) {
                $prepareClassSessions[$session->id] = $session->instructor->name
                    . " - " . $session->subject->name
                    . " - (" . $session->from_time . " - " . $session->to_time .")" ;
            }
            $data["classroomClasses"] = $prepareClassSessions;
        }

        if ($request->filled("classroomClass")) {
            $classSessions = $this->classroomClassRepository->getSessions($this->classroomClassRepository->find($request->get("classroomClass")));
            $prepareClassSessions = [];
            foreach ($classSessions as $session) {
                $prepareClassSessions[$session->id] = $session->from_date;
            }
            $data["classSessions"] = $prepareClassSessions;
        }

        $data["classrooms"] = $this->classroomRepository->listClassroomsNamesIDs(\auth()->user()->branch_id);
        $data["page_title"] = trans("navigation.Media Library");
        $data["mediaLibrary"] = $this->preparationRepository->getBranchMediaLibrary($branch, $request);

        return view("school_supervisor.preparations.mediaLibrary", $data);
    }

    public function getSingleMedia(PreparationMedia $media)
    {
        $data["media"] = $media;
        $data["mediaType"] = MediaEnums::getTypeExtensionsIconDisplay($media->extension)["extension"];
        $data['students'] = $media->student()->paginate(10);


//        dd($data);
        return view("school_supervisor.preparations.singleMedia", $data);
    }

    public function getSessionPreparation(ClassroomClassSession $session)
    {
        $data["session"] = $session;
        $data["page_title"] =  trans("session_preparation.View Session Preparation");

        return view("school_supervisor.preparations.SessionPreparation", $data);
    }
}
