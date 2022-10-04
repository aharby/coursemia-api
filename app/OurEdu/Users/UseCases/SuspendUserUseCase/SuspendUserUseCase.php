<?php


namespace App\OurEdu\Users\UseCases\SuspendUserUseCase;


use App\OurEdu\Users\Repository\UserRepositoryInterface;
use Carbon\Carbon;

class SuspendUserUseCase implements SuspendUserUseCaseInterface
{

    /**
     * @param UserRepositoryInterface $userRepository
     * @param int $id
     * @return bool
     */
    //Should Work like toggle of suspend user if not suspended and remove suspend if suspended
    public function suspendUser(UserRepositoryInterface $userRepository , int $id): bool {

        $row = $userRepository->findOrFail($id);

        if ($row->suspended_at) {

          return $userRepository->update($row , ['suspended_at' => null]);
        } else {

           return $userRepository->update($row , ['suspended_at' => Carbon::now()]);
        }
    }

}
