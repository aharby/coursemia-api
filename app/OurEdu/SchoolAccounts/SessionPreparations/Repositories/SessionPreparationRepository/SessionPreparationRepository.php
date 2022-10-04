<?php


namespace App\OurEdu\SchoolAccounts\SessionPreparations\Repositories\SessionPreparationRepository;


use App\OurEdu\GarbageMedia\MediaEnums;
use App\OurEdu\SchoolAccounts\Classroom;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\SchoolAccounts\SchoolAccounts\SchoolAccount;
use App\OurEdu\SchoolAccounts\SessionPreparations\Models\PreparationMedia;
use App\OurEdu\SchoolAccounts\SessionPreparations\Models\SessionPreparation;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as SessionAuth;

class SessionPreparationRepository implements SessionPreparationRepositoryInterface
{

    /**
     * @var SessionPreparation
     */
    private $model;

    public function __construct(SessionPreparation $sessionPreparation)
    {
        $this->model = $sessionPreparation;
    }

    public function create(array $data): ?SessionPreparation
    {
        return $this->model->create($data);
    }

    /**
     * @param SessionPreparation $sessionPreparation
     * @param $data
     * @return bool
     */
    public function update(SessionPreparation $sessionPreparation,$data): bool
    {
        return $sessionPreparation->update($data);
    }

    /**
     * @param SchoolAccountBranch $branch
     * @param Request|null $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|mixed
     */
    public function getBranchMediaLibrary(SchoolAccountBranch $branch, Request $request=null)
    {
        $mediaLibrary = PreparationMedia::query()
            ->whereHas("sessionPreparation", function (Builder $sessionPreparation) use ($branch, $request) {
                $sessionPreparation->whereHas("classroom", function (Builder $classroom) use ($branch) {
                    $classroom->where("branch_id", "=", $branch->id);
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
            ->with(["sessionPreparation" => function (BelongsTo $preparations) {
                $preparations->with("subject", "session");
            }])
            ->where("library", "=", 1);

        return $mediaLibrary->paginate(env('PAGE_LIMIT', 20));
    }

    /**
     * @param SchoolAccount $school
     * @param Request|null $request
     * @return mixed
     */
    public function getBranchesMediaLibrary(array $branchesIDs, Request $request = null)
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
            ->with(["sessionPreparation" => function (BelongsTo $preparations) {
                $preparations->with("subject", "session");
            }]);

        if ($request->filled("subject_id")) {
            $mediaLibrary->where("subject_id", "=", $request->get("subject_id"));
        }

        return $mediaLibrary->paginate(env('PAGE_LIMIT', 20));
    }


    /**
     * @param SchoolAccount $school
     * @param Request|null $request
     * @return mixed
     */
    public function getInstructorBranchesMediaLibrary(array $branchesIDs, Request $request = null)
    {

        $mediaLibrary = PreparationMedia::query()
            ->where("library", "=", 1)
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

                if ($request->filled("grade_class_id")) {
                    $sessionPreparation->whereHas("subject", function (Builder $subject) use ($request) {
                        $subject->where("grade_class_id", "=", $request->get("grade_class_id"));
                    });
                }

            })
            ->with(["sessionPreparation" => function (BelongsTo $preparations) {
                $preparations->with("subject", "session", "classroom");
            }])
            ->where(function (Builder $query) {
                $query->where("library", "=", 1)
                ->orWhereHas("sessionPreparation", function (Builder $preparation) {
                    $preparation->where("created_by", "=", SessionAuth::id());
                });
            });

        if ($request->filled("subject_id")) {
            $mediaLibrary->where("subject_id", "=", $request->get("subject_id"));
        }

        return $mediaLibrary->paginate(env('PAGE_LIMIT', 20));
    }

    public function getInstructorMediaLibrary(User $instructor, Request $request)
    {
        $mediaLibrary = PreparationMedia::query()

            ->with("sessionPreparation.classroom.branchEducationalSystemGradeClass.gradeClass")
            ->whereHas("sessionPreparation", function (Builder $sessionPreparation) use ($instructor, $request) {

                $sessionPreparation->whereHas("session", function (Builder $session) use ($instructor, $request) {
                    $session->where("instructor_id", '=', $instructor->id);

                    if ($request->filled("from")) {
                        $session->where("from", ">=", $request->get("from"));
                    }

                    if ($request->filled("to")) {
                        $session->where("to", "<=", Carbon::parse($request->get("to"))->addDay());
                    }
                });

                if ($request->filled("classroom_id")) {
                    $sessionPreparation->where("classroom_id", "=", $request->get("classroom_id"));
                }

                if ($request->filled("grade_class_id")) {
                    $sessionPreparation->whereHas("classroom", function (Builder $classroom) use ($request) {
                        $classroom->whereHas("branchEducationalSystemGradeClass.gradeClass", function (Builder $gradeClass) use ($request) {
                            $gradeClass->where("id", "=", $request->get("grade_class_id"));
                        });
                    });
                }
            });

        if ($request->filled("subject_id")) {
            $mediaLibrary->where("subject_id", "=", $request->get("subject_id"));
        }

        $mediaLibrary = $mediaLibrary->paginate(env('PAGE_LIMIT',20));

        return $mediaLibrary;
    }

    /**
     * @param Classroom $classroom
     * @param Request $request
     * @return Builder[]|Collection|mixed
     */
    public function getStudentMediaLibrary(Classroom $classroom, Request $request)
    {
        $mediaLibrary = PreparationMedia::query()
            ->where("library", "=", 1)
            ->whereHas("sessionPreparation", function (Builder $sessionPreparation) use ($classroom, $request) {
                $sessionPreparation->where("classroom_id", "=", $classroom->id);

                if ($request->filled("from") || $request->filled("to")) {
                    $sessionPreparation->whereHas("session", function (Builder $session) use ($request) {
                        if ($request->filled("from")) {
                            $session->where("from", ">=", $request->get("from"));
                        }

                        if ($request->filled("to")) {
                            $session->where("to", "<=", Carbon::parse($request->get("to"))->addDay());
                        }
                    });
                }
            });

        if ($request->filled("subject_id")) {
            $mediaLibrary->where("subject_id", "=", $request->get("subject_id"));
        }

        if ($request->filled("type")) {
            $mediaLibrary->whereIn("extension",  MediaEnums::getTypeExtensions($request->get("type")));
        }

        $mediaLibrary = $mediaLibrary->paginate(env('PAGE_LIMIT',20));

        return $mediaLibrary;
    }
}
