<?php


namespace App\OurEdu\SchoolAccounts\SessionPreparations\Repositories\SessionPreparationRepository;


use App\OurEdu\SchoolAccounts\Classroom;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\SchoolAccounts\SchoolAccounts\SchoolAccount;
use App\OurEdu\SchoolAccounts\SessionPreparations\Models\PreparationMedia;
use App\OurEdu\SchoolAccounts\SessionPreparations\Models\SessionPreparation;
use App\OurEdu\Users\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

interface SessionPreparationRepositoryInterface
{
    /**
     * @param array $data
     * @return SessionPreparation|null
     */
    public function create(array $data): ?SessionPreparation;


    /**
     * @param SessionPreparation $sessionPreparation
     * @param $data
     * @return bool
     */
    public function update(SessionPreparation $sessionPreparation,$data): bool;

    /**
     * @param SchoolAccountBranch $branch
     * @param Request|null $request
     * @return mixed
     */
    public function getBranchMediaLibrary(SchoolAccountBranch $branch, Request $request=null);

    /**
     * @param SchoolAccount $school
     * @param Request|null $request
     * @return mixed
     */
    public function getBranchesMediaLibrary(array $branchesIDs, Request $request=null);

    /**
     * @param SchoolAccount $school
     * @param Request|null $request
     * @return mixed
     */
    public function getInstructorBranchesMediaLibrary(array $branchesIDs, Request $request=null);

    public function getInstructorMediaLibrary(User $instructor, Request $request);

    /**
     * @param Classroom $classroom
     * @param Request $request
     * @return Builder[]|Collection|mixed
     */
    public function getStudentMediaLibrary(Classroom $classroom, Request $request);
}
