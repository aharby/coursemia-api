<?php

namespace App\OurEdu\SchoolAccounts\SchoolAccounts\Repository;

use App\OurEdu\Invitations\Models\ParentStudent;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\SchoolAccounts\SchoolAccounts\SchoolAccount;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SchoolAccountRepository
{
    protected $model;

    public function __construct(SchoolAccount $schoolAccount)
    {
        $this->model = $schoolAccount;
    }

    public function all(): LengthAwarePaginator
    {
        return $this->model->orderBy('id', 'DESC')->paginate(env('PAGE_LIMIT', 20));
    }

    public function allWith(array $with): LengthAwarePaginator
    {
        return $this->model->orderBy('id', 'DESC')->with($with)->paginate(env('PAGE_LIMIT', 20));
    }

    public function find(int $id): SchoolAccount
    {

        return $this->model->find($id);
    }

    public function findWith(int $id, array $with): SchoolAccount
    {

        return $this->model->with($with)->find($id);
    }

    public function getEducationalSystemsIds(): array
    {
        return $this->model->educationalSystems()->pluck('id')->toArray() ?? [];
    }

    public function getGradeClassesIds(): array
    {
        return $this->model->gradeClasses()->pluck('id')->toArray() ?? [];
    }
//    public function getEducationalTermsIds(): array
//    {
//        return $this->model->educationalTer->pluck('id')->toArray() ?? [];
//    }


    public function create(array $attributes): SchoolAccount
    {
        return $this->model->create($attributes);
    }

    public function update(int $id, array $attributes): bool
    {

        return $this->model->find($id)->update($attributes);
    }

    public function delete(int $id): bool
    {
        $schoolAccount = SchoolAccount::query()->find($id);
        $studentsId = User::query()->where('type', UserEnums::STUDENT_TYPE)->where('school_id', $id)->pluck('id')->toArray();
        // unrelate parent data
        ParentStudent::query()->whereIn('student_id', $studentsId)->delete();
        // delete student and assessment manager
        User::query()->where('school_id', $id)->delete();
        // manager
        $schoolAccount->manager()->delete();
        $branchesIDs = SchoolAccountBranch::query()->where('school_account_id', $schoolAccount->id)->pluck('id')->toArray();
        $supervisorsIds = SchoolAccountBranch::query()->where('school_account_id', $schoolAccount->id)->pluck('supervisor_id')->toArray();
        $leadersIds = SchoolAccountBranch::query()->where('school_account_id', $schoolAccount->id)->pluck('leader_id')->toArray();
        $supervisorsIdAndLeadersIds = array_merge($supervisorsIds, $leadersIds);
        // academic coordinator and school instructor
        User::query()->whereIn('branch_id', $branchesIDs)->delete();
        // supervisor and leader
        User::query()->whereIn('id', $supervisorsIdAndLeadersIds)->delete();
        // educational supervisor
        User::query()->whereHas(
            'branches',
            function ($builder) use ($branchesIDs) {
                $builder->whereIn('branch_user.branch_id', $branchesIDs);
            }
        )->delete();
        SchoolAccountBranch::query()->where('school_account_id', $schoolAccount->id)->delete();
        return $this->model->find($id)->delete();
    }

    public function pluck(): Collection
    {
        return $this->model->with('translations')->pluck('name', 'id');
    }

    public function attachEducationalSystems(array $ids)
    {
        $ids = array_filter(
            $ids,
            function ($var) {
                return !is_null($var);
            }
        );
        return $this->model->educationalSystems()->attach($ids);
    }

    public function createUpdateEducationalSystems(array $ids)
    {
        $ids = array_filter(
            $ids,
            function ($var) {
                return !is_null($var);
            }
        );
        return $this->model->educationalSystems()->sync($ids);
    }

    public function attachGradeClasses(array $ids)
    {
        $ids = array_filter(
            $ids,
            function ($var) {
                return !is_null($var);
            }
        );
        return $this->model->gradeClasses()->attach($ids);
    }

    public function createUdateGradeClasses(array $ids)
    {
        $ids = array_filter(
            $ids,
            function ($var) {
                return !is_null($var);
            }
        );
        return $this->model->gradeClasses()->sync($ids);
    }

    /**
     * @param array $ids
     * @return array
     */
    public function createUpdateEducationalTerms(array $ids): array
    {
        $ids = array_filter(
            $ids,
            function ($var) {
                return !is_null($var);
            }
        );
        return $this->model->educationalTerms()->sync($ids);
    }

    /**
     * @param array $ids
     * @return array
     */
    public function createUpdateAcademicYears(array $ids): array
    {
        $ids = array_filter(
            $ids,
            function ($var) {
                return !is_null($var);
            }
        );
        return $this->model->academicYears()->sync($ids);
    }
}
