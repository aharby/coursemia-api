<?php


namespace App\OurEdu\SchoolAdmin\SchoolAccountBranches\Repositories;

use App\OurEdu\EducationalSystems\EducationalSystem;
use App\OurEdu\GeneralQuizzes\Enums\QuestionsPublicStatusesEnums;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizQuestionBank;
use App\OurEdu\GradeClasses\GradeClass;
use App\OurEdu\Roles\Role;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\SchoolAccounts\SchoolAccountEducationalSystem;
use App\OurEdu\SchoolAccounts\SchoolAccounts\SchoolAccount;
use App\OurEdu\SchoolAdmin\Models\SchoolAdmin;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;

class SchoolBranchRepository
{

    public function findWith(int $id, array $with): SchoolAccountBranch
    {
        return SchoolAccountBranch::with($with)->find($id);
    }

    public function find(int $id): ?SchoolAccountBranch
    {
        return SchoolAccountBranch::find($id);
    }


    public function getBranchesByCurrentSchoolAccountId($schoolId)
    {
        return SchoolAccountBranch::where(
            'school_account_id',
            $schoolId
        )->with('schoolAccount');
    }


    public function update(int $id, array $attributes): bool
    {

        return SchoolAccountBranch::find($id)->update($attributes);
    }

    public function attachEducationalSystems(SchoolAccountBranch $schoolAccountBranch ,array $ids)
    {
        $ids = array_filter($ids, function ($var) {
            return !is_null($var);
        });
        return $schoolAccountBranch->educationalSystems()->sync($ids);
    }

    public function attachGradeClasses(SchoolAccountBranch $schoolAccountBranch, array $ids)
    {
        $ids = array_filter($ids, function ($var) {
            return !is_null($var);
        });
        return $schoolAccountBranch->gradeClasses()->attach($ids);
    }


    /**
     * @param $branchId
     * @param $educationalSystemId
     * @return mixed
     */
    public function getBranchEducationalSystem($branchId, $educationalSystemId)
    {
        $branch = SchoolAccountBranch::find($branchId);
        return $branch->branchEducationalSystem()->where('educational_system_id', $educationalSystemId)->first();
    }

    public function pluckBySchoolAccountId($schoolAccountId)
    {
        $educationalSystemIds = SchoolAccountEducationalSystem::where('school_account_id',$schoolAccountId)->pluck('educational_system_id');
        return EducationalSystem::whereIn('educational_systems.id',$educationalSystemIds)->with('translations')->listsTranslations('name')->pluck('name', 'id');
    }


    public function getSchoolDefaultRole($schoolAccountId)
    {
        return Role::query()->where("school_account_id", "=", $schoolAccountId)->first();
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
    
    public function setSubjectPermissions(Subject $subject, array $data, SchoolAccount $school = null)
    {

        if (!$school) {
            $user = Auth::user();
            $school = $user->schoolAdmin->currentSchool;
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
}
