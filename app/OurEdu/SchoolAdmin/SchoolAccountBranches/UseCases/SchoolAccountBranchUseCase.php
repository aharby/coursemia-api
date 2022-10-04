<?php

namespace App\OurEdu\SchoolAdmin\SchoolAccountBranches\UseCases;

// use App\OurEdu\Roles\Repository\RoleRepositoryInterface;
// use App\OurEdu\SchoolAccounts\SchoolAccountBranches\Repository\SchoolAccountBranchesRepository;
// use App\OurEdu\SchoolAccounts\SchoolAccounts\Repository\SchoolAccountRepository;

use App\OurEdu\SchoolAdmin\SchoolAccountBranches\Repositories\SchoolBranchRepository;
use App\OurEdu\Users\Repository\UserRepository;
// use App\OurEdu\Users\Repository\UserRepositoryInterface;
use App\OurEdu\Users\UseCases\SendActivationMailUseCase\SendActivationMailUseCaseInterface;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Support\Arr;

class SchoolAccountBranchUseCase
{
    /**
     * @var SchoolAccountRepository
     */
    private $schoolAccountBranchRepository;

    public function __construct(
        SchoolBranchRepository $schoolAccountBranchRepository
    ) {
        $this->schoolAccountBranchRepository = $schoolAccountBranchRepository;
    }

    /**
     * @return int
     */
    private function generateConfirmToken(): int
    {
        $token = rand(000000, 999999);
        if (User::where('confirm_token', $token)->exists()) {
            return $this->generateConfirmToken();
        }
        return $token;
    }

    /**
     * @param array $data
     * @param $id
     * @return bool
     */
    public function update(array $data, $id): bool
    {
        $branchData = Arr::except($data, ['leader_id', 'supervisor_id']);
        if ($this->schoolAccountBranchRepository->update($id, $branchData)) {
            // $branchRepo = new SchoolAccountBranchesRepository($this->schoolAccountBranchRepository->find($id));
            if (isset($data['educational_systems']) && is_array($data['educational_systems']) && count($data['educational_systems'])) {
                $this->schoolAccountBranchRepository->attachEducationalSystems($data['educational_systems']);
            }

            $schoolAccount = $this->schoolAccountBranchRepository->find($id)->schoolAccount;

            $defaultRole = $this->schoolAccountBranchRepository->getSchoolDefaultRole($schoolAccount->id);
            if (isset($data['supervisor_id'])) {
                // create account of the new school account manager with new type = 'school_manager'
                $alreadyExistSupervisor =  User::where('username', $data['supervisor_id'])->first();
                if (!$alreadyExistSupervisor) {
                    $supervisorData = [
                        'first_name' => '',
                        'language' => 'ar',
                        'username' => $data['supervisor_id'],
                        'password' => $data['supervisor_id'],
                        'type' => UserEnums::SCHOOL_SUPERVISOR,
                        'branch_id' => $id,
                        'confirm_token' => $this->generateConfirmToken()
                    ];
                    $supervisorUser = User::create($supervisorData);
                    // then send him notification mail of activate his account
                    if ($supervisorUser) {
                        $this->schoolAccountBranchRepository->update($id, ['supervisor_id' => $supervisorUser->id]);
                        $this->updateRole(["role_id" => $defaultRole->id], $supervisorUser);
                    }
                }
            }
            if (isset($data['leader_id'])) {
                $alreadyExistLeader =  User::where('username', $data['leader_id'])->first();
                if (!$alreadyExistLeader) {
                    $leaderUser = User::create([
                        'first_name' => '',
                        'language' => 'ar',
                        'username' => $data['leader_id'],
                        'password' => $data['leader_id'],
                        'type' => UserEnums::SCHOOL_LEADER,
                        'branch_id' => $id,
                        'confirm_token' => $this->generateConfirmToken()
                    ]);
                    // 
                    if ($leaderUser) {
                        $this->schoolAccountBranchRepository->update($id, ['leader_id' => $leaderUser->id]);
                        $this->updateRole(["role_id" => $defaultRole->id], $leaderUser);
                    }
                }
            }
            return true;
        }
        return false;
    }

    // public function updatePassword(array $data, $id): bool
    // {
    // $user = $this->userRepository->find($id);
    // if($user)
    //     $user->update(['password'=>$data['password']]);
    // return true;
    // }

    public function updateRole(array $data, User $user): bool
    {
        if ($user) {
            $user->update(['role_id' => $data['role_id']]);
        }
        return true;
    }
}
