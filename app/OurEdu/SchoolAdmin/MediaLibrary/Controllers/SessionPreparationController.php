<?php


namespace App\OurEdu\SchoolAdmin\MediaLibrary\Controllers;


use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\GarbageMedia\MediaEnums;
use App\OurEdu\SchoolAccounts\ClassroomClass\Repositories\ClassroomClassRepositoryInterface;
use App\OurEdu\SchoolAccounts\Repositories\ClassroomRepositoryInterface;
use App\OurEdu\SchoolAccounts\SchoolAccounts\SchoolAccount;
use App\OurEdu\SchoolAccounts\SessionPreparations\Models\PreparationMedia;
use App\OurEdu\SchoolAdmin\InstructorAttendance\Repositories\ClassroomRepository;
use App\OurEdu\SchoolAdmin\MediaLibrary\Repositories\MediaRepository;
use Illuminate\Http\Request;

class SessionPreparationController extends BaseController
{
    private MediaRepository $mediaRepository;

    private ClassroomRepository $classroomRepository;

    /**
     * @var ClassroomClassRepositoryInterface
     */
    private $classroomClassRepository;

    public function __construct(
        ClassroomClassRepositoryInterface $classroomClassRepository,
        MediaRepository $mediaRepository
    ) {
        $this->classroomClassRepository = $classroomClassRepository;
        $this->mediaRepository = $mediaRepository;
        $this->classroomRepository = new ClassroomRepository();
    }

    public function getMediaLibrary(Request $request)
    {
        $user = auth()->user();
        $schoolAccount = SchoolAccount::find($user->schoolAdmin->current_school_id);

        $data = [];
        $schoolBranches = $this->mediaRepository->getBranchesBySchoolAccountPluck(
            $schoolAccount
        )->toArray();

        $data["branches"] = $schoolBranches;
        $filterBranches = array_keys($schoolBranches);

        if ($request->filled("branch")) {
            $filterBranches = [$request->get("branch")];
            $data["classrooms"] = $this->classroomRepository->listClassroomsNamesIDs($request->get("branch"));
        }

        if ($request->filled("classroom")) {
            $classSessions = $this->classroomClassRepository->getByClassroom(
                $this->classroomRepository->find($request->get("classroom"))
            );
            $prepareClassSessions = [];
            foreach ($classSessions as $session) {
                $prepareClassSessions[$session->id] = $session->instructor->name
                    . " - " . $session->subject->name
                    . " - (" . $session->from_time . " - " . $session->to_time . ")";
            }
            $data["classroomClasses"] = $prepareClassSessions;
        }

        if ($request->filled("classroomClass")) {
            $classSessions = $this->classroomClassRepository->getSessions(
                $this->classroomClassRepository->find($request->get("classroomClass"))
            );
            $prepareClassSessions = [];
            foreach ($classSessions as $session) {
                $prepareClassSessions[$session->id] = $session->from_date;
            }
            $data["classSessions"] = $prepareClassSessions;
        }

        $data["page_title"] = trans("navigation.Media Library");
        $data["mediaLibrary"] = $this->mediaRepository->getBranchesMediaLibrary($filterBranches, $request);

        return view("school_admin.preparations.mediaLibrary", $data);
    }

    public function getSingleMedia(PreparationMedia $media)
    {
        $data["media"] = $media;
        $data["mediaType"] = MediaEnums::getTypeExtensionsIconDisplay($media->extension)["extension"];
        $data['students'] = $media->student()->paginate(10);

        return view("school_admin.preparations.singleMedia", $data);
    }
}
