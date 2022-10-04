<?php

namespace Database\Seeders;

use App\OurEdu\GeneralQuizzes\Models\GeneralQuizQuestionBank;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\Repository\SchoolAccountBranchesRepository;
use App\OurEdu\SchoolAccounts\SchoolAccounts\SchoolAccount;
use Illuminate\Database\Seeder;

class SetTheDefaultPermissionForOldBankQuestionsSeeder extends Seeder
{
    /**
     * @var SchoolAccountBranchesRepository
     */
    private SchoolAccountBranchesRepository $schoolAccountBranchesRepository;

    /**
     * SetTheDefaultPermissionForOldBankQuestionsSeeder constructor.
     * @param SchoolAccountBranchesRepository $schoolAccountBranchesRepository
     */
    public function __construct(SchoolAccountBranchesRepository $schoolAccountBranchesRepository)
    {
        $this->schoolAccountBranchesRepository = $schoolAccountBranchesRepository;
    }


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $schoolAccounts = SchoolAccount::query()
            ->get();

        foreach ($schoolAccounts as $schoolAccount) {
            $branches = $schoolAccount->branches;
            foreach($branches as $branch) {
                $subjects = $this->schoolAccountBranchesRepository->branchSubjects($branch, []);
                foreach($subjects as $subject) {
                    $data["branch_id"] = $branch->id;
                    $data["permission_scope"] = "school_scope";

                    $isSubjectHasPermission = $subject->branchQuestionsPermissions()
                        ->where("school_account_branches.id", "=", $data["branch_id"])
                        ->first();

                    if (!$isSubjectHasPermission) {
                        $this->schoolAccountBranchesRepository->setSubjectPermissions($subject, $data, $schoolAccount);
                    }
                }
            }
        }
    }
}
