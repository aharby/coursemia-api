<?php


namespace App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountManager\Controllers;


use App\OurEdu\GarbageMedia\MediaEnums;
use App\OurEdu\SchoolAccounts\ClassroomClass\Repositories\ClassroomClassRepositoryInterface;
use App\OurEdu\SchoolAccounts\Repositories\ClassroomRepositoryInterface;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\Repository\SchoolAccountBranchesRepository;
use App\OurEdu\SchoolAccounts\SchoolAccounts\SchoolAccount;
use App\OurEdu\SchoolAccounts\SessionPreparations\Models\PreparationMedia;
use App\OurEdu\SchoolAccounts\SessionPreparations\Repositories\SessionPreparationRepository\SessionPreparationRepository;
use App\OurEdu\SchoolAccounts\SessionPreparations\Repositories\SessionPreparationRepository\SessionPreparationRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionPreparationController extends \App\OurEdu\BaseApp\Controllers\BaseController
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
     * @var SchoolAccountBranchesRepository
     */
    private $schoolAccountBranchesRepository;

    /**
     * SessionPreparationController constructor.
     * @param SessionPreparationRepositoryInterface $preparationRepository
     * @param ClassroomRepositoryInterface $classroomRepository
     * @param ClassroomClassRepositoryInterface $classroomClassRepository
     * @param SchoolAccountBranchesRepository $schoolAccountBranchesRepository
     */
    public function __construct(
        SessionPreparationRepositoryInterface $preparationRepository,
        ClassroomRepositoryInterface $classroomRepository,
        ClassroomClassRepositoryInterface $classroomClassRepository,
        SchoolAccountBranchesRepository $schoolAccountBranchesRepository
    )
    {
        $this->preparationRepository = $preparationRepository;
        $this->classroomRepository = $classroomRepository;
        $this->classroomClassRepository = $classroomClassRepository;
        $this->schoolAccountBranchesRepository = $schoolAccountBranchesRepository;
    }

    public function getMediaLibrary(Request $request)
    {
        $data = [];
        $schoolBranches = $this->schoolAccountBranchesRepository->getBranchesBySchoolAccountManagerPluck(Auth::id())->toArray();
        $data["branches"] = $schoolBranches;
        $filterBranches = array_keys($schoolBranches);

        if ($request->filled("branch")) {
            $filterBranches = [$request->get("branch")];
            $data["classrooms"] = $this->classroomRepository->listClassroomsNamesIDs($request->get("branch"));

        }

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

        $data["page_title"] = trans("navigation.Media Library");
        $data["mediaLibrary"] = $this->preparationRepository->getBranchesMediaLibrary($filterBranches, $request);

        return view("school_account_manager.preparations.mediaLibrary", $data);
    }

    public function getSingleMedia(PreparationMedia $media)
    {
        $data["media"] = $media;
        $data["mediaType"] = MediaEnums::getTypeExtensionsIconDisplay($media->extension)["extension"];
        $data['students'] = $media->student()->paginate(10);

        return view("school_account_manager.preparations.singleMedia", $data);
    }
}
