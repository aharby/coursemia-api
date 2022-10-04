<?php

namespace App\OurEdu\SchoolAdmin\MediaLibrary\Repositories;

use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\SchoolAccounts\SchoolAccounts\SchoolAccount;
use App\OurEdu\SchoolAccounts\SessionPreparations\Models\PreparationMedia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class MediaRepository
{

    protected $model;

    public function __construct(SchoolAccountBranch $schoolAccountBranches)
    {
        $this->model = $schoolAccountBranches;
    }

    public function getBranchesBySchoolAccountPluck(SchoolAccount $schoolAccount): Collection
    {
        return $this->model->with('translations')
            ->where('school_account_id', $schoolAccount->id)
            ->orderBy('id', 'DESC')
            ->pluck('name', 'id');
    }

    /**
     * @param SchoolAccount $school
     * @param Request|null $request
     * @return mixed
     */
    public function getBranchesMediaLibrary(array $branchesIDs, Request $request = null): LengthAwarePaginator
    {
        $mediaLibrary = PreparationMedia::query()
            ->whereHas("sessionPreparation", function (Builder $sessionPreparation) use ($branchesIDs, $request) {
                $sessionPreparation->whereHas("classroom", function (Builder $classroom) use ($branchesIDs) {
                    $classroom->whereIn("branch_id", $branchesIDs);
                });

                if ($request->filled("classroom")) {
                    $sessionPreparation->where("classroom_id", "=", $request->get("classroom"));
                }

                if ($request->filled("classroomClass")) {
                    $sessionPreparation->where("classroom_class_id", "=", $request->get("classroomClass"));
                }

                if ($request->filled("classSession")) {
                    $sessionPreparation->where("classroom_session_id", "=", $request->get("classSession"));
                }
            })
            ->with([
                "sessionPreparation" => function (BelongsTo $preparations) {
                    $preparations->with("subject", "session");
                }
            ]);

        if ($request->filled("subject_id")) {
            $mediaLibrary->where("subject_id", "=", $request->get("subject_id"));
        }

        return $mediaLibrary->paginate(env('PAGE_LIMIT', 20));
    }
}