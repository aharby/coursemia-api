<?php


namespace App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountManager\UseCases\users;


use App\OurEdu\Users\Repository\UserRepositoryInterface;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Http\Request;

class SchoolUsersUseCase implements SchoolUsersUseCaseInterface
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * SchoolUsersUseCase constructor.
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param Request $request
     * @return User
     */
    public function create(Request $request): User
    {
        $branchId = null;

        if ($request->get("type") != UserEnums::EDUCATIONAL_SUPERVISOR) {
            $branchId = $request->get("branch_id");
        }

        $supervisorData = [
            'first_name' => $request->get("first_name"),
            'last_name' => $request->get("last_name"),
            'language' => 'ar',
            'username' => $request->get("username"),
            'password' => $request->get("username"),
            'email' => $request->get("email"),
            'type' => $request->get("type"),
            "role_id" => $request->get("role_id"),
            'confirm_token' => $this->generateConfirmToken(),
        ];

        if($request->get('type') == UserEnums::ASSESSMENT_MANAGER){
            $supervisorData['school_id'] = auth()->user()->schoolAccount->id;
        }

        if(is_array($branchId) && count($branchId) == 1){
            $supervisorData['branch_id'] = $branchId[0];
        }elseif(!is_array($branchId)){
            $supervisorData['branch_id'] = $branchId;
        }

        $user = $this->userRepository->create($supervisorData);

        if (!$branchId) {
            $user->branches()->sync($request->get("branch_id"));
        }

        return $user;
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

}
