<?php

namespace App\OurEdu\SchoolAccounts\SchoolAccountBranches\Repository;

use App\OurEdu\GeneralQuizzes\Enums\QuestionsPublicStatusesEnums;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizQuestionBank;
use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropData;
use App\OurEdu\SchoolAccounts\BranchEducationalSystem;
use App\OurEdu\SchoolAccounts\SchoolAccounts\SchoolAccount;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\OurEdu\EducationalSystems\EducationalSystem;
use App\OurEdu\GradeClasses\GradeClass;
use App\OurEdu\Subjects\Models\Subject;
use Illuminate\Support\Facades\Auth;

class SchoolAccountBranchesRepository
{
    protected $model;

    public function __construct(SchoolAccountBranch $schoolAccountBranches)
    {
        $this->model = $schoolAccountBranches;
    }

    public function all(): LengthAwarePaginator
    {
        return $this->model->orderBy('id', 'DESC')->paginate(env('PAGE_LIMIT', 20));
    }

    public function allWith(array $with): LengthAwarePaginator
    {
        return $this->model->orderBy('id', 'DESC')->with($with)->paginate(env('PAGE_LIMIT', 20));
    }


    public function getBranchesBySchoolAccountManagerPaginate(int $accountManagerId)
    {
        $schoolAccount = SchoolAccount::where('manager_id', $accountManagerId)->first();
        if (!$schoolAccount) {
            return [];
        }

        return $this->model->where('school_account_id', $schoolAccount->id)->orderBy('id', 'DESC')->paginate(env('PAGE_LIMIT', 20));
    }


    public function getBranchesBySchoolAccountManagerPluck(int $accountManagerId)
    {
        $schoolAccount = SchoolAccount::where('manager_id', $accountManagerId)->first();
        if (!$schoolAccount) {
            return new Collection();
        }

        return $this->model->with('translations')
            ->where('school_account_id', $schoolAccount->id)
            ->orderBy('id', 'DESC')
            ->pluck('name', 'id');
    }

    public function getBranchesBySchoolAccountManager(int $accountManagerId)
    {
        $schoolAccount = SchoolAccount::where('manager_id', $accountManagerId)->first();
        if (!$schoolAccount) {
            return [];
        }

        return $this->model->where('school_account_id', $schoolAccount->id)->orderBy('id', 'DESC')->get();
    }
    public function find(int $id): ?SchoolAccountBranch
    {
        return $this->model->find($id);
    }

    public function findWith(int $id, array $with): SchoolAccountBranch
    {
        return $this->model->with($with)->find($id);
    }


    public function create(array $attributes): SchoolAccountBranch
    {

        return $this->model->create($attributes);
    }

    public function update(int $id, array $attributes): bool
    {

        return $this->model->find($id)->update($attributes);
    }

    public function delete(int $id): bool
    {

        return $this->model->find($id)->delete();
    }

    public function pluck(): Collection
    {
        return $this->model->with('translations')->listsTranslations('name')->pluck('name', 'id');
    }

    public function attachEducationalSystems(array $ids)
    {
        $ids = array_filter($ids, function ($var) {
            return !is_null($var);
        });
        return $this->model->educationalSystems()->sync($ids);
    }

    public function attachGradeClasses(array $ids)
    {
        $ids = array_filter($ids, function ($var) {
            return !is_null($var);
        });
        return $this->model->gradeClasses()->attach($ids);
    }

    /**
     * @param $branchId
     * @param $educationalSystemId
     * @return mixed
     */
    public function getBranchEducationalSystem($branchId, $educationalSystemId)
    {
        $branch = $this->model->find($branchId);
        return $branch->branchEducationalSystem()->where('educational_system_id', $educationalSystemId)->first();
    }

    public function getSchoolUsers(SchoolAccount $schoolAccount)
    {
        $availableUserTypes = array_keys(UserEnums::schoolAccountUsers());
        $availableUserTypes[] = UserEnums::SCHOOL_LEADER;
        $availableUserTypes[] = UserEnums::SCHOOL_SUPERVISOR;
        $availableUserTypes[] = UserEnums::ASSESSMENT_MANAGER;

        return User::query()->where(function (Builder $query) use ($schoolAccount) {
            $query->whereHas("branch", function (Builder $builder) use ($schoolAccount) {
                $builder->where("school_account_id", "=", $schoolAccount->id);
            })
            ->orWhereHas("schoolSupervisor", function (Builder $builder) use ($schoolAccount) {
                $builder->where("school_account_id", "=", $schoolAccount->id);
            })
            ->orWhereHas("schoolLeader", function (Builder $builder) use ($schoolAccount) {
                $builder->where("school_account_id", "=", $schoolAccount->id);
            })
            ->orWhereHas('branches', function (Builder $schoolAccountBranch) use ($schoolAccount) {
                $schoolAccountBranch->where("school_account_id", "=", $schoolAccount->id);
            })
            ->orWhere('school_id',$schoolAccount->id);
        })->with("branch", "branches")
          ->whereIn("type", $availableUserTypes)
          ->paginate(env('PAGE_LIMIT', 20));
    }

    public function getUserAttends(SchoolAccount $schoolAccount)
    {
        // dd($schoolAccount->branches);
        $availableUserTypes[] = UserEnums::SCHOOL_LEADER;
        $availableUserTypes[] = UserEnums::SCHOOL_SUPERVISOR;
        $availableUserTypes[] = UserEnums::ACADEMIC_COORDINATOR;
        $availableUserTypes[] = UserEnums::EDUCATIONAL_SUPERVISOR;

        $date = $this->handleSearchDate();

        $users =  User::query()->where(function (Builder $query) use ($schoolAccount) {
                $query->whereHas("branches", function (Builder $builder) use ($schoolAccount) {
                    $builder->whereIn('branch_id',$schoolAccount->branches->pluck('id')->toArray());
                })->orWhereHas("branch", function (Builder $builder) use ($schoolAccount) {
                    $builder->where("school_account_id", "=", $schoolAccount->id);
                })
                ->orWhereHas("schoolSupervisor", function (Builder $builder) use ($schoolAccount) {
                    $builder->where("school_account_id", "=", $schoolAccount->id);
                })
                ->orWhereHas("schoolLeader", function (Builder $builder) use ($schoolAccount) {
                    $builder->where("school_account_id", "=", $schoolAccount->id);
                });
            })
            ->when(request()->filled("branch"), function (Builder $query) {
                $query->whereHas('branches',function(Builder $builder){
                    $builder->where('branch_id','=',request()->get("branch"));
                });
            })

            ->when(request()->filled("type"), function (Builder $query) {
                $query->where("type", "=", request()->get("type"));
            })
            ->whereIn("type", $availableUserTypes)
            ->withCount([
                'VCRSessionsPresence' => function($query) use ($date){
                    $query->whereHas('vcrSession',function ($q) use ($date){
                        $q->where("time_to_end", "<=", $date['to']);
                        $q->when($date["from"], function (Builder $query) use ($date) {
                            $query->where("time_to_end", ">=", $date['from']);
                        });
                        $q->when(request()->filled("branch"),function (Builder $query){
                            $query->whereHas('classroom.branch',function($qu){
                                $qu->where('id',request('branch'));
                            });
                        });
                        $q->when(request()->filled("classroom"), function (Builder $query)  {
                            $query->where("classroom_id", "=", request("classroom"));
                        });
                        $q->when(request()->filled("subject"), function (Builder $query)  {
                            $query->where("subject_id", "=", request("subject"));
                        });
                    });
                }
            ])
            ->with(['VCRSessionsPresence','VCRSessionsPresence.vcrSession.instructor','VCRSessionsPresence.vcrSession.subject','VCRSessionsPresence.vcrSession.classroom','VCRSessionsPresence.vcrSession.classroomClassSession'])
            ->with([
                'VCRSessionsPresence' => function ($vsrSession) use ($date)
                {
                    $vsrSession->whereHas('vcrSession' , function($session) use ($date){
                        $session->where("time_to_end", "<=", $date['to']);
                        $session->when($date["from"], function (Builder $query) use ($date) {
                            $query->where("time_to_end", ">=", $date['from']);
                        });
                        $session->when(request()->filled("branch"),function (Builder $query){
                            $query->whereHas('classroom.branch',function($qu){
                                $qu->where('id',request('branch'));
                            });
                        });
                        $session->when(request()->filled("classroom"), function (Builder $query)  {
                            $query->where("classroom_id", "=", request("classroom"));
                        });
                        $session->when(request()->filled("subject"), function (Builder $query)  {
                            $query->where("subject_id", "=", request("subject"));
                        });
                    });
                }
            ]);

//            $users->whereHas('VCRSessionsPresence.vcrSession',function(Builder $session) use ($date){
//                $session->where("time_to_end", "<=", $date['to']);
//                $session->when($date["from"], function (Builder $query) use ($date) {
//                    $query->where("time_to_end", ">=", $date['from']);
//                });
//                $session->when(request()->filled("classroom"), function (Builder $query)  {
//                    $query->where("classroom_id", "=", request("classroom"));
//                });
//                $session->when(request()->filled("subject"), function (Builder $query)  {
//                    $query->where("subject_id", "=", request("subject"));
//                });
//            });

            return $users->paginate(env('PAGE_LIMIT', 20));
    }

    public function handleSearchDate() {
        $from = null;
        if (request()->filled("from")) {
            $from = Carbon::parse(request()->get("from"));
            $from->format("Y-m-d 00:00:00");
        }

        $to = Carbon::now();
        $to = $to->format('Y-m-d H:i:s');
        if (request()->filled("to")) {
            $to = Carbon::parse(request()->get("to"));
            $to = $to->format('Y-m-d 23:59:00');
            if (Carbon::today()->lte($to)) {
                $to = Carbon::now();
                $to = $to->format('Y-m-d H:i:s');
            }
        }

        return ['from' => $from , 'to' => $to ];
    }


    public function getEducationalSystemsByBranch($branchId)
    {
        $branch = $this->model->find($branchId);
        $branchEducationalSystems = array_unique($branch->branchEducationalSystem()->pluck('educational_system_id')->toArray());
        return EducationalSystem::whereIn('id',$branchEducationalSystems)->get();
    }

    public function getEducationalSystemsByBranches($branchesID)
    {
        $branchEducationalSystems = BranchEducationalSystem::query()
            ->whereIn("branch_id", $branchesID)
            ->pluck('educational_system_id')
            ->toArray();

        return EducationalSystem::whereIn('id',$branchEducationalSystems)->get();
    }
    public function getGradeClassesByEducationalSystem($educationalSystemId)
    {
        return GradeClass::where('educational_system_id',$educationalSystemId)->get();
    }

    public function getSubjectsByGradeClass($gradeClassIds){
        return Subject::whereIn('grade_class_id',$gradeClassIds)->with('gradeClass')->get();
    }

    public function branchSubjects(SchoolAccountBranch $branch, array $data) {
        $educationalSystemIds = isset($data['educational_system'])? [$data['educational_system']]: $branch->educationalSystems()->pluck("educational_systems.id")->toArray();
        $branchEducationalSystems = $branch->branchEducationalSystem()->get();

        $gradesIds = isset($data['grade_class'])? [$data['grade_class']]: GradeClass::query()
            ->whereHas("branchEducationalSystemGradeClass", function (Builder $branchEducationalSystem) use ($branchEducationalSystems) {
                $branchEducationalSystem->whereIn("branch_educational_system_id", $branchEducationalSystems->pluck("id")->toArray());
            })->pluck("id")
            ->toArray();

        $subjects = Subject::query()
            ->with([
                "educationalSystem",
                "gradeClass",
            ])
            ->with(["branchQuestionsPermissions" => function (BelongsToMany $query) use ($branch) {
                $query->where("school_account_branches.id", "=", $branch->id);
            }])
            ->whereIn('educational_system_id', $educationalSystemIds)
            ->whereIn('grade_class_id', $gradesIds)
            ->whereIn('academical_years_id', $branchEducationalSystems->pluck("academic_year_id")->toArray())
            ->whereIn('educational_term_id', $branchEducationalSystems->pluck("educational_term_id")->toArray())
            ;

        return $subjects->paginate(env("PAGE_LIMIT"));
    }

    public function setSubjectPermissions(Subject $subject, array $data, SchoolAccount $school = null)
    {

        if (!$school) {
            $schoolManager = Auth::user();
            $school = $schoolManager->schoolAccount;
        }

        $relatedSchool = $subject->branchQuestionsPermissions()
            ->where("school_account_branches.id", "=", $data["branch_id"])
            ->first();

        if (!$school) {
            return false;
        }

        $permissions = [
            "branch_scope" => isset($data['permission_scope']) and $data['permission_scope'] == 'branch_scope',
            "grade_scope" => isset($data['permission_scope']) and $data['permission_scope'] == 'grade_scope',
            "school_scope" => isset($data['permission_scope']) and $data['permission_scope'] == "school_scope",
            "school_id" => $school->id,
        ];

        if($relatedSchool) {
            $subject->branchQuestionsPermissions()
                ->updateExistingPivot($data['branch_id'], $permissions);
        } else {
            $subject->branchQuestionsPermissions()
                ->attach($data['branch_id'], $permissions);
        }

        $barnchPermission = $subject->branchQuestionsPermissions()
            ->where("school_account_branches.id", "=", $data["branch_id"])
            ->first();

        if($barnchPermission){
            $subjectBranchPermission = $barnchPermission->pivot;
        }else{
            return false;
        }
        $permission = null;

        if ($subjectBranchPermission and $subjectBranchPermission->branch_scope) {
            $permission = QuestionsPublicStatusesEnums::BRANCH;
        }

        if ($subjectBranchPermission and $subjectBranchPermission->grade_scope) {
            $permission = QuestionsPublicStatusesEnums::GRADE;
        }

        if ($subjectBranchPermission and $subjectBranchPermission->school_scope) {
            $permission = QuestionsPublicStatusesEnums::SCHOOL;
        }


        if ($permission and $subjectBranchPermission) {
            GeneralQuizQuestionBank::query()
                ->where("public_status", "!=", QuestionsPublicStatusesEnums::PRIVATE)
                ->where("school_account_branch_id", "=", $subjectBranchPermission->branch_id)
                ->where("subject_id", "=", $subject->id)
                ->update(['public_status' => $permission]);
        }

        return true;
    }

    public function pluckBranchGrades(SchoolAccountBranch $branch)
    {
        $branchEducationalSystems = $branch->branchEducationalSystem()->get();

        $gradesIds = GradeClass::query()
            ->whereHas("branchEducationalSystemGradeClass", function (Builder $branchEducationalSystem) use ($branchEducationalSystems) {
                $branchEducationalSystem->whereIn("branch_educational_system_id", $branchEducationalSystems->pluck("id")->toArray());
            })
            ->get()
            ->pluck("title", "id")
            ->toArray();

        return $gradesIds;
    }


}
