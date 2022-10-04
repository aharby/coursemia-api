<?php
namespace App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\UseCases;

use App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\UseCases\EducationalSupervisorUseCaseInterface;
use App\OurEdu\Users\Repository\UserRepositoryInterface;
use Illuminate\Support\Str;
use App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\Requests\EducationalSupervisorRequest;


class EducationalSupervisorUseCase implements EducationalSupervisorUseCaseInterface
{

    private $userRepository;
    private $schoolAccountBranchesRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
        // $this->schoolAccountBranchesRepository = $schoolAccountBranchesRepository;
        $this->branch = null;
    }


    /**
     * @param Request $request
     * @return User
     */
    public function updateEducationalSupervisor(EducationalSupervisorRequest $request,$educationalSupervisor)
    {
        $userData = [
            "first_name" => $request->get("first_name"),
            "last_name" => $request->get("last_name"),
            "username" => $request->get("username"),
            "mobile" => $request->get("mobile"),
            "email" => $request->get("email"),
        ];

        if ($request->filled("password")) {
            $userData["password"] =$request->get("password");
            $educationalSupervisor->password =  $request->get("password");
            $educationalSupervisor->save();
        }

        $user = $this->userRepository->update($educationalSupervisor,$userData);
        if($user){
            $educationalSupervisor->educationalSupervisorSubjects()->detach();
            $requestSujects = $request->get('subjects') ?? [];
            foreach($requestSujects as $subject_gradeclass){
                $options = explode('-',$subject_gradeclass);
                $educationalSupervisor->educationalSupervisorSubjects()->attach($options[0],[
                    'edu_system_id'=>$request->get('educational_system_id'),
                    'grade_class_id'=>$options[1]
                ]);
            }
        }
        return $user;
    }


}
