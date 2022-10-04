<?php


namespace App\OurEdu\SchoolAccounts\SchoolAccounts\Admin\UseCases\SchoolAccountUseCases;


use App\OurEdu\Assessments\Jobs\AddUserToAssessmentJob;
use App\OurEdu\SchoolAccounts\SchoolAccounts\Repository\SchoolAccountRepository;
use App\OurEdu\Users\Repository\UserRepositoryInterface;
use App\OurEdu\Users\UseCases\CreateZoomUserUserCase\CreateZoomUserUseCaseInterface;
use App\OurEdu\Users\UseCases\SendActivationMailUseCase\SendActivationMailUseCaseInterface;
use App\OurEdu\Users\UserEnums;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SchoolAccountUseCase implements SchoolAccountUseCaseInterface
{


    /**
     * @var SchoolAccountRepository
     */
    private $schoolAccountRepository;
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;
    /**
     * @var SendActivationMailUseCaseInterface
     */
    private $sendActivationMailUseCaseInterface;
    /**
     * @var CreateZoomUserUseCaseInterface
     */
    private $createZoomUser;

    public function __construct(
        SchoolAccountRepository $schoolAccountRepository,
        UserRepositoryInterface $userRepository,
        SendActivationMailUseCaseInterface $sendActivationMailUseCase,
        CreateZoomUserUseCaseInterface $createZoomUser
    )
    {
        $this->schoolAccountRepository = $schoolAccountRepository;
        $this->userRepository = $userRepository;
        $this->sendActivationMailUseCaseInterface = $sendActivationMailUseCase;
        $this->createZoomUser = $createZoomUser;
    }

    /**
     * @param array $data
     * @return array
     */
    public function save(array $data): array
    {
        DB::beginTransaction();

        $schoolManagerUser = $this->userRepository->create([
            'first_name' => $data['name'],
            'language' => 'ar',
            'username' => $data['manager_id'],
            'password' => $data['manager_id'],
            'type' => UserEnums::SCHOOL_ACCOUNT_MANAGER,
            'is_active' => 1,
            'confirm_token' => $this->generateConfirmToken()
        ]);
        $createZoomUser = $this->createZoomUser->createUser($schoolManagerUser);
        if ($createZoomUser['error']) {
            DB::rollBack();
            return $createZoomUser;
        }
        AddUserToAssessmentJob::dispatch($schoolManagerUser);

        $data['manager_id'] = $schoolManagerUser->id;
        $data = Arr::except($data, ['first_name', 'language', 'email']);
        $schoolAccount = $this->schoolAccountRepository->create($data);

        if (!Str::contains($schoolAccount->logo, 'school-accounts/logo/')) {
            $schoolAccount->update(['logo' => 'school-accounts/logo/' . $schoolAccount->logo]);
        }
        $schoolAccountRepo = new SchoolAccountRepository($schoolAccount);

        $schoolAccountRepo->createUpdateEducationalSystems($data['educational_systems']);
        $schoolAccountRepo->createUdateGradeClasses($data['grade_classes']);
        $schoolAccountRepo->createUpdateEducationalTerms($data['educational_terms']);
        $schoolAccountRepo->createUpdateAcademicYears($data['academical_years']);
        DB::commit();

        return [
            'school_account' => $schoolAccount
        ];

    }


    /**
     * @param array $data
     * @param $id
     * @return bool
     */
    public function update(array $data, $id): bool
    {
        $schoolAccount = $this->schoolAccountRepository->find($id);

//        $schoolAccountRepo = new SchoolAccountRepository($schoolAccount);

        $this->schoolAccountRepository->update($id, $data);
        $updated = $this->schoolAccountRepository->find($id);

        if (!is_null($updated->logo) && !Str::contains($updated->logo, 'school-accounts/logo/')) {
            $schoolAccount->update(['logo' => 'school-accounts/logo/' . $updated->logo]);
        }
//        $schoolAccountRepo->createUpdateEducationalSystems($data['educational_systems']);
//        $schoolAccountRepo->createUdateGradeClasses($data['grade_classes']);
//        $schoolAccountRepo->createUpdateEducationalTerms($data['educational_terms']);
//        $schoolAccountRepo->createUpdateAcademicYears($data['academical_years']);
        return true;
    }

    /**
     * @return bool
     */
    private function generateConfirmToken()
    {
        $token = rand(000000, 999999);
        if ($this->userRepository->checkConfirmToken($token)) {
            return $this->generateConfirmToken();
        }
        return $token;
    }
}
