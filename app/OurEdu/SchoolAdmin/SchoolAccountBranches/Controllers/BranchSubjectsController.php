<?php


namespace App\OurEdu\SchoolAdmin\SchoolAccountBranches\Controllers;


use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\Repository\SchoolAccountBranchesRepository;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\SchoolAdmin\SchoolAccountBranches\Repositories\SchoolBranchRepository;
use App\OurEdu\Subjects\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class BranchSubjectsController extends BaseController
{
    public function __construct()
    {
        $this->schoolAccountBranchesRepository = new SchoolBranchRepository();
    }

    public function index(Request $request, SchoolAccountBranch $branch)
    {
        $data['subjects'] = $this->schoolAccountBranchesRepository->branchSubjects($branch, $request->all());
        $data['branch'] = $branch;
        $data['page_title'] = trans("school-account-branches.Subject Question Bank Permissions");
        $data['educational_systems'] = $branch->educationalSystems()->get()->pluck("name", "id")->toArray();
        $data['grade_classes'] = $this->schoolAccountBranchesRepository->pluckBranchGrades($branch);

        return view("school_admin.school_account_branches.question_bank_permissions", $data);
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
