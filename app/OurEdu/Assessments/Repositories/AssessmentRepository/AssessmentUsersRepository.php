<?php

namespace App\OurEdu\Assessments\Repositories\AssessmentRepository;
use App\OurEdu\SchoolAccounts\SchoolAccounts\SchoolAccount;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\Assessments\Models\AssessmentAnswer;
use Illuminate\Database\Eloquent\Builder;
use App\OurEdu\Assessments\Models\AssessmentAssessor;
use App\OurEdu\Assessments\Models\AssessmentUser;
use App\OurEdu\Assessments\Models\AssessmentAssessee;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class AssessmentUsersRepository implements AssessmentUsersRepositoryInterface
{

     /**
     * @param User $assessor
     * @return LengthAwarePaginator
     */
    public function getAssessmentsByAssessor(User $assessor): LengthAwarePaginator
    {
        $assessments = Assessment::query()
            ->whereHas("assessors", function (Builder $users) use ($assessor) {
                $users->where("user_id", "=", $assessor->id);
            });

        $assessments = $this->queryByUserRoleBranchAndSubject($assessments,$assessor,'assessees');

        $assessments = $assessments
            ->whereNotNull('published_at')
            ->where('start_at', '<=', now())
            ->where("end_at", ">=", now())
            ->orderByDesc("start_at")
            ->orderByDesc("id")
            ->paginate(env("PAGE_LIMIT",20));

        return $assessments;
    }



    /**
     * @param User $assessee
     * @return LengthAwarePaginator
     */
    public function getAssessmentsByAssessee(User $assessee,$filters = []): LengthAwarePaginator
    {
        return AssessmentUser::with('assessment')
            ->where('assessee_id', $assessee->id)
            ->where('is_finished', 1)
            ->whereHas('assessment',function($query) use($filters){
                $query->when(isset($filters["assessor_type"]),function($q) use($filters){
                    $q->where('assessor_type',$filters['assessor_type']);
                })
                ->when(isset($filters["from_date"]) && !isset($filters["to_date"]),function($q) use($filters){
                    $q->whereDate('start_at', '>=', \Carbon\Carbon::parse($filters['from_date'])->format('Y-m-d H-i-s'));
                })
                ->when(!isset($filters["from_date"]) && isset($filters["to_date"]),function($q) use($filters){

                    $q->whereDate('start_at', '<=', \Carbon\Carbon::parse($filters['to_date'])->format('Y-m-d H-i-s'));
                })
                ->when(isset($filters["from_date"]) && isset($filters["to_date"]),function($q) use($filters) {
                    $q->whereBetween('start_at',[\Carbon\Carbon::parse($filters['from_date'])->format('Y-m-d H-i-s'),\Carbon\Carbon::parse($filters['to_date'])->addDay()->subMinutes(1)->format('Y-m-d H-i-s')]);
                });
            })

            ->groupBy('assessment_id', 'assessee_id')
            ->selectRaw('assessment_id, assessee_id, avg(score) as avg_score, avg(total_mark) as avg_total_mark')
            ->paginate(env("PAGE_LIMIT",20));
        }


    public function findAssessorAssessment(int $assessmentId,int $assessorId):?AssessmentAssessor{
        return AssessmentAssessor::where('assessment_id',$assessmentId)
                    ->where('user_id',$assessorId)->firstOrFail();
    }

    public function findAssesseeAssessment(int $assessmentId,int $assesseeId):?AssessmentAssessee{
        return AssessmentAssessee::where('assessment_id',$assessmentId)
                ->where('user_id',$assesseeId)->firstOrFail();
    }

    public function startAssessment(array $data){
        AssessmentUser::create($data);
    }

    public function getUserAssessment($assessmentId,$assesseeId,$assessorId):?AssessmentUser
    {
        return AssessmentUser::with('answers')
            ->where('assessment_id',$assessmentId)
            ->where('user_id',$assessorId)
            ->where('assessee_id',$assesseeId)
            ->where('is_finished', "!=", 1)
            ->first();
    }

    public function getAssessorAnswersScore($assessmentId , $assessorId){
        return AssessmentAnswer::where('user_id' , $assessorId)
            ->where('assessment_id', $assessmentId)->sum('score');
    }

    public function update($assessmentUserId , $data)
    {
        return AssessmentUser::findOrFail($assessmentUserId)->update($data);
    }

    public function getAssessorAnswersCount($assessmentId,$assessorId){
        return AssessmentAnswer::where('user_id' , $assessorId)
            ->where('assessment_id', $assessmentId)->count();
    }

    public function getAssessorAssessees(Assessment $assessment, User $assessor)
    {
        $assessees = $assessment->assessees()
            ->where("users.id", "!=", $assessor->id);

        $assessees = $this->queryUserBranchByUserRole($assessees,$assessor);

        if (in_array($assessor->type ,[UserEnums::EDUCATIONAL_SUPERVISOR,UserEnums::SCHOOL_INSTRUCTOR])) {
            $assessees = $this->queryFilterUserBySubject($assessees,$assessor);
        }

        return $assessees->paginate(env("PAGE_LIMIT"));
    }

    public function getAssesseeAssessors($assessmentId, $assesseeId)
    {
        return AssessmentUser::with(['assessor','assessment'])
        ->where('assessment_id',$assessmentId)
        ->where('assessee_id',$assesseeId)
        ->where('is_finished', 1)
        ->paginate(env("PAGE_LIMIT",20));
    }

    public function getAssesseeByAssessorId(Assessment $assessment ,int $assessorId,$isPaginate = true){
        $user = auth()->user();
        $query = AssessmentUser::with(['assessor','assessee','assessment','assessment.rates'])
            ->where("assessee_id", "!=", Auth::id())
            ->where('assessment_id',$assessment->id)
            ->where('user_id',$assessorId)
            ->where("is_finished", "=", 1);

        if($user->type !== UserEnums::ASSESSMENT_MANAGER){
            $query = $this->queryByUserRoleBranchAndSubject($query,$user,'assessee');
        }

        $query->groupBy("assessment_id", "assessee_id", "user_id")
            ->selectRaw("assessment_id, assessee_id, user_id, AVG(score) as score, AVG(total_mark) as total_mark");

        return $isPaginate?$query->paginate(env("PAGE_LIMIT")):$query->get();
    }

    public function getAssessedUsersOfAssessor(Assessment $assessment, int $assessorId, User $user)
    {
        $query = AssessmentUser::query()
            ->where('assessment_id', $assessment->id)
            ->where('user_id', $assessorId)
            ->finished();

        $query = $this->queryByUserRoleBranchAndSubject($query, $user, 'assessee');

        return $query->get();
    }

    /**
     * @param Assessment $assessment
     * @param User $assessor
     * @param User $assessee
     * @param bool $isPaginate
     * @return LengthAwarePaginator|Builder[]|Collection
     */
    public function getAssesseeDetailsByAssessor(Assessment $assessment, User $assessor, User $assessee, bool $isPaginate=true)
    {
        $assesseeAssessments = AssessmentUser::query()
            ->where("assessment_id", "=", $assessment->id)
            ->where("user_id", "=", $assessor->id)
            ->where("assessee_id", "=", $assessee->id)
            ->where("is_finished", "=", 1);

        if (!$isPaginate) {
            return $assesseeAssessments->get();
        }

        return $assesseeAssessments->paginate(env("PAGE_LIMIT", 20));
    }

    /**
     * this method return the only assessors that has assess the assessments
     *
     * @param Assessment $assessment
     * @param bool $isPaginate
     * @return LengthAwarePaginator|Builder[]|Collection
     */
    public function getAssessmentAssessors(Assessment $assessment,bool $isPaginate=true,User $user = null){
        $user = $user ?? auth()->user();
        $query = AssessmentAssessor::with('user','assessment')
            ->whereHas("assessment.assessmentUsers", function (Builder $assessmentUser) {
                $assessmentUser
                    ->whereColumn("assessment_users.user_id", "=", "assessment_assessors.user_id")
                    ->finished();
            })
            ->where('assessment_id',$assessment->id);

        if($user->type !== UserEnums::ASSESSMENT_MANAGER){
            $query = $this->queryByUserRoleBranchAndSubject($query,$user,'user');
        }

        return $isPaginate?$query->paginate(env("PAGE_LIMIT")):$query->get();
    }


    /**
     * @param User $user
     * @param bool $isPaginate
     * @return LengthAwarePaginator|Collection
     */
    public function viewerAssessments(User $user, $isPaginate = true,$filters = [])
    {
        $assessments = $user->assessmentsAsViewer()
            ->with("authResultViewer")
            ->when(isset($filters["assessor_type"]),function($q) use($filters){
                $q->where('assessor_type',$filters['assessor_type']);
            })
            ->when(isset($filters["assessee_type"]),function($q) use($filters){
                $q->where('assessee_type',$filters['assessee_type']);
            })
            ->when(isset($filters["from_date"]) && !isset($filters["to_date"]),function($q) use($filters){
                $q->whereDate('start_at', '>=', \Carbon\Carbon::parse($filters['from_date'])->format('Y-m-d H-i-s'));
            })
            ->when(!isset($filters["from_date"]) && isset($filters["to_date"]),function($q) use($filters){

                $q->whereDate('start_at', '<=', \Carbon\Carbon::parse($filters['to_date'])->format('Y-m-d H-i-s'));
            })
            ->when(isset($filters["from_date"]) && isset($filters["to_date"]),function($q) use($filters) {
                $q->whereBetween('start_at',[\Carbon\Carbon::parse($filters['from_date'])->format('Y-m-d H-i-s'),\Carbon\Carbon::parse($filters['to_date'])->addDay()->subMinutes(1)->format('Y-m-d H-i-s')]);
            })
            ->when(!isset($filters['from_date']) && !isset($filters['to_date']),function($q){
                $q->where('start_at', '<=', now());
            })
            ->where('published_before', true)
            ->orderByDesc("start_at")
            ->orderByDesc("id")
            ->withPivot(['total_assesses_count', 'assessed_assesses_count']);

        if ($isPaginate) {
            return $assessments->paginate(env("PAGE_LIMIT"));
        }

        return $assessments->get();
    }


    public function schoolAdminViewAssessmentsResult(User $user, $isPaginate = true,$filters = [])
    {
        $branches = $this->getUserSchoolBranches($user);
        return $query->where(function (Builder $userBranches) use ($branches) {
            $userBranches->whereHas("branches", function (Builder $builder) use ($branches) {
                $builder->whereIn("school_account_branches.id", $branches); // for educational supervisor
            })->orWhereHas('schoolAccount.branches',function(Builder $builder)use($branches){
                $builder->whereIn("school_account_branches.id", $branches); // for school manager
            })->orWhereIn('branch_id',$branches);
        });
        
        $assessments = $user->assessmentsAsViewer()
            ->with("authResultViewer")
            ->when(isset($filters["assessor_type"]),function($q) use($filters){
                $q->where('assessor_type',$filters['assessor_type']);
            })
            ->when(isset($filters["assessee_type"]),function($q) use($filters){
                $q->where('assessee_type',$filters['assessee_type']);
            })
            ->when(isset($filters["from_date"]) && !isset($filters["to_date"]),function($q) use($filters){
                $q->whereDate('start_at', '>=', \Carbon\Carbon::parse($filters['from_date'])->format('Y-m-d H-i-s'));
            })
            ->when(!isset($filters["from_date"]) && isset($filters["to_date"]),function($q) use($filters){

                $q->whereDate('start_at', '<=', \Carbon\Carbon::parse($filters['to_date'])->format('Y-m-d H-i-s'));
            })
            ->when(isset($filters["from_date"]) && isset($filters["to_date"]),function($q) use($filters) {
                $q->whereBetween('start_at',[\Carbon\Carbon::parse($filters['from_date'])->format('Y-m-d H-i-s'),\Carbon\Carbon::parse($filters['to_date'])->addDay()->subMinutes(1)->format('Y-m-d H-i-s')]);
            })
            ->when(!isset($filters['from_date']) && !isset($filters['to_date']),function($q){
                $q->where('start_at', '<=', now());
            })
            ->where('published_before', true)
            ->orderByDesc("start_at")
            ->orderByDesc("id")
            ->withPivot(['total_assesses_count', 'assessed_assesses_count']);

        if ($isPaginate) {
            return $assessments->paginate(env("PAGE_LIMIT"));
        }

        return $assessments->get();
    }


    private function queryByUserRoleBranchAndSubject($query,$user,$relation)
    {
        return $query->whereHas($relation,function($query) use($user){
            $query = $this->queryUserBranchByUserRole($query,$user);
            if(in_array($user->type,[UserEnums::SCHOOL_INSTRUCTOR,UserEnums::EDUCATIONAL_SUPERVISOR])){
                $query = $this->queryFilterUserBySubject($query,$user);
            }
        });
    }


    // query filter by user's branch according to each user role
    private function queryUserBranchByUserRole($query,$user)
    {
        // get branches for each user role
        $branches = $this->getUserSchoolBranches($user);
        return $query->where(function (Builder $userBranches) use ($branches) {
            $userBranches->whereHas("branches", function (Builder $builder) use ($branches) {
                $builder->whereIn("school_account_branches.id", $branches); // for educational supervisor
            })->orWhereHas('schoolAccount.branches',function(Builder $builder)use($branches){
                $builder->whereIn("school_account_branches.id", $branches); // for school manager
            })->orWhereIn('branch_id',$branches);
        });
    }



    // for educational supervisor and school instructor
    private function queryFilterUserBySubject($query,$user)
    {
        $userSubjects = $this->getUserSubjects($user);
        return $query->where(function (Builder $query) use ($userSubjects) {
            $query
                ->whereHas("schoolInstructorSubjects", function (Builder $subjects) use ($userSubjects) {
                    $subjects->whereIn("subjects.id", $userSubjects);
                })
                ->orWhereHas("educationalSupervisorSubjects", function (Builder $subjects) use ($userSubjects) {
                    $subjects->whereIn("subjects.id", $userSubjects);
                })
                ->orWhereNotIn("type", [UserEnums::EDUCATIONAL_SUPERVISOR, UserEnums::SCHOOL_INSTRUCTOR]);
        });
    }

    private function getUserSchoolBranches($user)
    {
        if ($user->type == UserEnums::SCHOOL_ACCOUNT_MANAGER ) {
            $branches = $user->schoolAccount()->firstOrFail()
                ->branches()->pluck("school_account_branches.id")->toArray();
        }
        else if ($user->type == UserEnums::EDUCATIONAL_SUPERVISOR ) {
            $branches = $user->branches()->pluck("school_account_branches.id")->toArray();
        }else if($user->type == UserEnums::SCHOOL_ADMIN){
            $branches = $user->schoolAdmin->currentSchool->branches()->pluck("school_account_branches.id")->toArray();
        } else {
            $branches = [$user->schoolAccountBranchType->id];
        }
        return $branches;
    }

    // get subject by user role ( for school instructor and educational supervisor)
    private function getUserSubjects($user)
    {
        $subjects = [];
        if ($user->type == UserEnums::EDUCATIONAL_SUPERVISOR) {
            $subjects = $user->educationalSupervisorSubjects()->pluck("subjects.id")->toArray();
        } elseif($user->type == UserEnums::SCHOOL_INSTRUCTOR) {
            $subjects = $user->schoolInstructorSubjects()->pluck("subjects.id")->toArray();
        }

        return $subjects;
    }

    public function getAssesseeByViewerId(Assessment $assessment ,User $viewer)
    {
        $user = $viewer ?? auth()->user();
        $query = AssessmentUser::with(['assessor','assessee','assessment','assessment.rates'])
            ->where('assessment_id',$assessment->id)
            ->where("is_finished", "=", 1)
            ->where('assessee_id' , '!=' , $user->id);

        if($user->type !== UserEnums::ASSESSMENT_MANAGER){
            $query = $this->queryByUserRoleBranchAndSubject($query,$user,'assessee');
        }

        $query->groupBy("assessment_id", "assessee_id", "user_id");

        return $query;
    }

    public function getAllUserFinishedAssessment($assessmentId,$assesseeId)
    {
        return AssessmentUser::with('answers')
                ->where('assessment_id',$assessmentId)
                ->where('assessee_id',$assesseeId)
                ->where('is_finished', "=", 1)
                ->get();
    }

    public function getAllAssesseeByViewerId(Assessment $assessment ,User $viewer)
    {
        $user = $viewer ?? auth()->user();
        $query = $assessment->assessees()
        ->where('users.id','!=' , $user->id);

        if($user->type !== UserEnums::ASSESSMENT_MANAGER) {
            $query = $this->queryUserBranchByUserRole($query, $user);
            if (in_array($user->type, [UserEnums::SCHOOL_INSTRUCTOR, UserEnums::EDUCATIONAL_SUPERVISOR])) {
                $query = $this->queryFilterUserBySubject($query, $user);
            }
        }

        return $query;
    }

    public function getUserCountedAssessment($assessmentId,$assesseeId,$assessorId)
    {
        return AssessmentUser::where('assessment_id',$assessmentId)
            ->where('user_id',$assessorId)
            ->where('assessee_id',$assesseeId)
            ->where('is_finished',1)
            ->where('counted',1);

    }

    public function getAllViewersByAssesse(Assessment $assessment ,User $assess)
    {
        $user = $assess ?? auth()->user();
        $query = $assessment->resultViewers();

//        if ($user->type !== UserEnums::ASSESSMENT_MANAGER) {
//            $query = $this->queryUserBranchByUserRole($query, $user);
//            if (in_array($user->type, [UserEnums::SCHOOL_INSTRUCTOR, UserEnums::EDUCATIONAL_SUPERVISOR])) {
//                $query = $this->queryFilterUserBySubject($query, $user);
//            }
//        }

        return $query;
    }

    public function getGroupedAssessAssessors($assessmentId, $assesseeId)
    {
        $query =  AssessmentUser::with(['assessor','assessment','assessee'])
            ->where('assessment_id',$assessmentId)
            ->where('assessee_id',$assesseeId)
            ->where('is_finished', 1)
            ->groupBy("assessment_id", "assessee_id", "user_id")
            ->selectRaw("assessment_id, assessee_id, user_id ,AVG(score) as average_score, AVG(total_mark) as ave_total_mark");

        return $query->paginate(env("PAGE_LIMIT",20));
    }


    public function getAssessmentIdsOfAssessorGeneralType(User $user, SchoolAccount $school)
    {
        return Assessment::query()
            ->whereNotNull('published_at')
            ->where('school_account_id', $school->id)
            ->where('end_at','>=',now())
            ->where('assessor_type_is_general', true)
            ->where('assessor_type',$user->type)
            ->pluck('id')->toArray();
    }

    public function getAssessmentIdsOfAssesseeGeneralType(User $user, SchoolAccount $school)
    {
        return Assessment::query()
            ->whereNotNull('published_at')
            ->where('school_account_id', $school->id)
            ->where('end_at','>=',now())
            ->where('assessee_type_is_general', true)
            ->where('assessee_type',$user->type)
            ->pluck('id')->toArray();
    }

    public function getAssessmentIdsOfResultViewerGeneralType(User $user, SchoolAccount $school)
    {
        return Assessment::query()
            ->whereNotNull('published_at')
            ->where('school_account_id', $school->id)
            ->where('end_at','>=',now())
            ->where('assessment_viewer_type_is_general', true)
            ->where(function ($type) use ($user) {
                $type->where('assessment_viewer_type',$user->type)
                    ->orwhereHas('resultViewerTypes', function ($user_type) use ($user) {
                        $user_type->where('user_type',$user->type);
                    });
            })
            ->pluck('id')->toArray();
    }
}
