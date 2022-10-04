<?php
namespace App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountManager\UseCases\SchoolAccountBranchUseCase;

use App\OurEdu\Assessments\Jobs\AddUserToAssessmentJob;
use App\OurEdu\Roles\Repository\RoleRepositoryInterface;
use App\OurEdu\SchoolAccounts\BranchEducationalSystem;
use App\OurEdu\SchoolAccounts\BranchEducationalSystemGradeClass;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\Repository\SchoolAccountBranchesRepository;
use App\OurEdu\SchoolAccounts\SchoolAccounts\Repository\SchoolAccountRepository;
use App\OurEdu\Users\Repository\UserRepository;
use App\OurEdu\Users\Repository\UserRepositoryInterface;
use App\OurEdu\Users\UseCases\CreateZoomUserUserCase\CreateZoomUserUseCaseInterface;
use App\OurEdu\Users\UseCases\SendActivationMailUseCase\SendActivationMailUseCaseInterface;
use App\OurEdu\Users\UserEnums;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SchoolAccountBranchUseCase implements SchoolAccountBranchUseCaseInterface
{
    /**
     * @var SchoolAccountRepository
     */
    private $schoolAccountBranchRepository;
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;
    /**
     * @var SendActivationMailUseCaseInterface
     */
    private $sendActivationMailUseCase;
    /**
     * @var RoleRepositoryInterface
     */
    private $roleRepository;
    /**
     * @var CreateZoomUserUseCaseInterface
     */
    private $createZoomUser;


    public function __construct(
        SchoolAccountBranchesRepository $schoolAccountBranchRepository,
        UserRepositoryInterface $userRepository,
        SendActivationMailUseCaseInterface $sendActivationMailUseCase,
        RoleRepositoryInterface $roleRepository,
        CreateZoomUserUseCaseInterface $createZoomUser
  )
    {
        $this->schoolAccountBranchRepository = $schoolAccountBranchRepository;
        $this->userRepository = $userRepository;
        $this->sendActivationMailUseCase = $sendActivationMailUseCase;
        $this->roleRepository = $roleRepository;
        $this->createZoomUser = $createZoomUser;
    }

    /**
     * @return int
     */
    private function generateConfirmToken():int
    {
        $token = rand(000000, 999999);
        if ($this->userRepository->checkConfirmToken($token)) {
            return $this->generateConfirmToken();
        }
        return $token;
    }

    /**
     * @param array $data
     * @param $id
     * @return bool
     */
    public function update(array $data, $id): array
    {
        $branchData = Arr::except($data,['leader_id','supervisor_id']);
        if ($this->schoolAccountBranchRepository->update($id, $branchData)){
            $branchRepo = new SchoolAccountBranchesRepository($this->schoolAccountBranchRepository->find($id));
            if (isset($data['educational_systems']) && is_array($data['educational_systems']) && count($data['educational_systems'])) {
                $branchRepo->attachEducationalSystems($data['educational_systems']);
            }
            $schoolAccount = $this->schoolAccountBranchRepository->find($id)->schoolAccount;
            $defaultRole = $this->roleRepository->getSchoolDefaultRole($schoolAccount);

            if(isset($data['supervisor_id'])){
                // create account of the new school account manager with new type = 'school_manager'
                $alreadyExistSupervisor = $this->userRepository->findByUsername($data['supervisor_id']);
                if(!$alreadyExistSupervisor) {
//                    DB::beginTransaction();
                    $supervisorData = [
                        'first_name' => '',
                        'language' => 'ar',
                        'username' => $data['supervisor_id'],
                        'password' => $data['supervisor_id'],
                        'type' => UserEnums::SCHOOL_SUPERVISOR,
                        'branch_id' => $id,
                        'confirm_token' => $this->generateConfirmToken()
                    ];

                    $supervisorUser = $this->userRepository->create($supervisorData);

                    // then send him notification mail of activate his account
                    if ($supervisorUser) {
                        $this->schoolAccountBranchRepository->update($id,['supervisor_id'=>$supervisorUser->id]);
                        $this->updateRole(["role_id" => $defaultRole->id], $supervisorUser->id);

//                        $createZoomUser = $this->createZoomUser->createUser($supervisorUser);
//                        if ($createZoomUser['error']) {
////                            DB::rollBack();
//                            Log::channel('slack')->error($createZoomUser['detail']);
//                            return $createZoomUser;
//                        }

//                        DB::commit();
                        AddUserToAssessmentJob::dispatch($supervisorUser);
                    }


                }
            }


            if(isset($data['leader_id'])){
                $alreadyExistLeader = $this->userRepository->findByUsername($data['leader_id']);
                if(!$alreadyExistLeader){
//                    DB::beginTransaction();

                    $leaderUser = $this->userRepository->create([
                        'first_name' =>'',
                        'language' => 'ar',
                        'username' => $data['leader_id'],
                        'password' => $data['leader_id'],
                        'type' => UserEnums::SCHOOL_LEADER,
                        'branch_id' => $id,
                        'confirm_token' => $this->generateConfirmToken()
                    ]);

                    if($leaderUser){
                        $this->schoolAccountBranchRepository->update($id,['leader_id'=>$leaderUser->id]);
                        $this->updateRole(["role_id" => $defaultRole->id], $leaderUser->id);

                        $createZoomUser = $this->createZoomUser->createUser($leaderUser);
//                        if ($createZoomUser['error']) {
//                            DB::rollBack();
//                            Log::channel('slack')->error($createZoomUser['detail']);
//                            return $createZoomUser;
//                        }
//                        DB::commit();
                        AddUserToAssessmentJob::dispatch($leaderUser);
                    }
                }
            }
            return ['status' => 201 , 'error' => false];
        }
        return ['status' => 500  , 'error' => true];
    }

    public function updatePassword(array $data, $id): bool
    {
        $user = $this->userRepository->find($id);
        if($user)
            $user->update(['password'=>$data['password']]);
        return true;
    }

    public function updateBranches(array $data, $id): bool
    {
        $user = $this->userRepository->find($id);

        if ($user and $user->type == UserEnums::EDUCATIONAL_SUPERVISOR) {
            $user->branches()->sync($data['branches']);
            $branches = $user->branches()->pluck('school_account_branches.id')->toArray();

            $branchEducationalSystems = BranchEducationalSystem::query()
                ->whereIn("branch_id", $branches)
                ->get();

            $branchesGradeClasses = BranchEducationalSystemGradeClass::query()
                ->whereIn(
                    "branch_educational_system_id",
                    $branchEducationalSystems->pluck("id")->toArray()
                )
                ->pluck("grade_class_id")
                ->toArray();

            $educationalSystems = $branchEducationalSystems
                ->pluck("educational_system_id")
                ->toArray();

            $subject = $user->educationalSupervisorSubjects()
                ->wherePivotIn("edu_system_id", array_unique($educationalSystems))
                ->wherePivotIn("grade_class_id", array_unique($branchesGradeClasses))
                ->pluck("subjects.id")
                ->toArray();

            $user->educationalSupervisorSubjects()->sync($subject);
        }

        return true;
    }


    public function updateRole(array $data, $id): bool
    {
        $user = $this->userRepository->find($id);
        if($user){
            $user->update(['role_id'=>$data['role_id']]);
        }
        return true;
    }
}
