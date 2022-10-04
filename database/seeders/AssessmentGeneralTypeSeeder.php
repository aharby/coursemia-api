<?php

namespace Database\Seeders;

use App\OurEdu\Assessments\Jobs\UpdateAssesseCountJob;
use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Illuminate\Database\Seeder;

class AssessmentGeneralTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $assessments =  Assessment::query()
            ->whereIn('id',[305,301,299,295,255,294,254,251,249,241,240,239,235,231,217,193,154,152,164,31])
            ->where('end_at','>=',now())
            ->get();

        foreach ($assessments as $assessment) {
            $this->syncAssessmentAssessors($assessment);
            $this->syncAssessmentAssessees($assessment);
            $this->syncAssessmentResultViewers($assessment);
        }
    }

    private function syncAssessmentAssessors(Assessment $assessment)
    {
        $assessorsIds = $assessment->assessors()->pluck('user_id')->toArray();
        $assessorTypeUserIds = $this->getUsersBySchoolAccountAndType($assessment->schoolAccount,$assessment->assessor_type,$assessorsIds);
        $assessment->assessors()->syncWithoutDetaching($assessorTypeUserIds);
    }

    private function syncAssessmentAssessees(Assessment $assessment)
    {
        $assesseesIds = $assessment->assessees()->pluck('user_id')->toArray();
        $assesseeUserIds = $this->getUsersBySchoolAccountAndType($assessment->schoolAccount,$assessment->assessee_type,$assesseesIds);
        $assessment->assessees()->syncWithoutDetaching($assesseeUserIds);
    }

    private function syncAssessmentResultViewers(Assessment $assessment)
    {
        $resultViewersIds = $assessment->resultViewers()->pluck('user_id')->toArray();
        if(!is_null($assessment->assessment_viewer_type))
        {
            $resultViewersUserIds = $this->getUsersBySchoolAccountAndType($assessment->schoolAccount,$assessment->assessment_viewer_type,$resultViewersIds);
            $assessment->resultViewers()->syncWithoutDetaching($resultViewersUserIds);
        }else{
            $viewerTypes = $assessment->resultViewerTypes->pluck('user_type')->toArray();
            foreach($viewerTypes as $type)
            {
                $resultViewersUserIds = $this->getUsersBySchoolAccountAndType($assessment->schoolAccount,$type,$resultViewersIds);
                $assessment->resultViewers()->syncWithoutDetaching($resultViewersUserIds);
            }
        }
    }



    private function getUsersBySchoolAccountAndType($schoolAccount,$userType,$usersIds)
    {
        $users = User::where('type',$userType)->whereNotIn('id',$usersIds);
        $users = match ($userType) {
            UserEnums::SCHOOL_ACCOUNT_MANAGER => $users->where('school_id',$schoolAccount->id),
            UserEnums::SCHOOL_LEADER => $users->whereHas('schoolLeader',function($query) use($schoolAccount){
                $query->where('school_account_id',$schoolAccount->id);
            }),
            UserEnums::SCHOOL_SUPERVISOR => $users->whereHas('schoolSupervisor',function($query) use($schoolAccount){
                $query->where('school_account_id',$schoolAccount->id);
            }),
            UserEnums::EDUCATIONAL_SUPERVISOR => $users->whereHas('branches',function($query)  use($schoolAccount){
                $query->where('school_account_id',$schoolAccount->id);
            }),
            UserEnums::SCHOOL_INSTRUCTOR=> $users->whereHas('branch',function($query)  use($schoolAccount){
                $query->where('school_account_id',$schoolAccount->id);
            }),
            UserEnums::ACADEMIC_COORDINATOR => $users->whereHas('branch',function($query)  use($schoolAccount){
                $query->where('school_account_id',$schoolAccount->id);
            }),
            default => false
        };

        if($users)
        {
            return $users->pluck('id')->toArray();
        }
    }
}
