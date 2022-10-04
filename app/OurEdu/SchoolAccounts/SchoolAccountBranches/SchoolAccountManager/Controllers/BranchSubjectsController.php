<?php


namespace App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountManager\Controllers;


use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\Repository\SchoolAccountBranchesRepository;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\Subjects\Models\Subject;
use Illuminate\Http\Request;

class BranchSubjectsController extends BaseController
{
    /**
     * @var SchoolAccountBranchesRepository
     */
    private SchoolAccountBranchesRepository $schoolAccountBranchesRepository;

    /**
     * BranchSubjectsController constructor.
     * @param SchoolAccountBranchesRepository $schoolAccountBranchesRepository
     */
    public function __construct(SchoolAccountBranchesRepository $schoolAccountBranchesRepository)
    {
        $this->schoolAccountBranchesRepository = $schoolAccountBranchesRepository;
    }

    public function index(Request $request, SchoolAccountBranch $branch)
    {
        $data['subjects'] = $this->schoolAccountBranchesRepository->branchSubjects($branch, $request->all());
        $data['branch'] = $branch;
        $data['page_title'] = trans("school-account-branches.Subject Question Bank Permissions");
        $data['educational_systems'] = $branch->educationalSystems()->get()->pluck("name", "id")->toArray();
        $data['grade_classes'] = $this->schoolAccountBranchesRepository->pluckBranchGrades($branch);

        return view("school_account_manager.school_account_branches.question_bank_permissions", $data);
    }

    public function questionsPermissionsBank(Request $request, Subject $subject)
    {
        $response = $this->schoolAccountBranchesRepository->setSubjectPermissions($subject, $request->all());

        if ($response) {
            return redirect()->back()->with(["success" => "Permissions Set Successful"]);
        }

        return redirect()->back()->with(["error" => "Error Happens"]);
    }
}
