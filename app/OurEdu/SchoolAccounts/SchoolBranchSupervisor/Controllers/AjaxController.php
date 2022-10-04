<?php


namespace App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\Controllers;


use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\Repository\SchoolAccountBranchesRepository;
use App\OurEdu\Users\Repository\UserRepositoryInterface;
use App\OurEdu\Users\User;
use Illuminate\Http\Request;

class AjaxController extends BaseController
{
    /**
     * @var SchoolAccountBranchesRepository
     */
    private $schoolAccountBranchesRepository;


    /**
     * SchoolAccountUsersController constructor.
     * @param SchoolAccountBranchesRepository $schoolAccountBranchesRepository
     */
    public function __construct(
        SchoolAccountBranchesRepository $schoolAccountBranchesRepository
    )
    {
        $this->schoolAccountBranchesRepository = $schoolAccountBranchesRepository;
    }

    public function getBranchEducationalSystem($branch_id){
        return response()->json([
            "status" => 200,
            "educationSystems" => $this->schoolAccountBranchesRepository->getEducationalSystemsByBranch($branch_id),
        ]);
    }
    public function getGradeClassesByEducationalSystem($educationalSystemId){
        return response()->json([
            "status" => 200,
            "gradeClasses" => $this->schoolAccountBranchesRepository->getGradeClassesByEducationalSystem($educationalSystemId),
        ]);
    }

    public function getSubjectsByGradeClass($gradeClassIds){
        $gradeClassIds = explode(',',$gradeClassIds);
        return response()->json([
            "status" => 200,
            "subjects" => $this->schoolAccountBranchesRepository->getSubjectsByGradeClass($gradeClassIds),
        ]);
    }

}
