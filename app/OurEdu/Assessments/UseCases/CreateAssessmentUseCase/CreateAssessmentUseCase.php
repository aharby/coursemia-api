<?php

namespace App\OurEdu\Assessments\UseCases\CreateAssessmentUseCase;

use App\OurEdu\Assessments\AssessmentManager\AssignAssessmentRelationDataJob;
use App\OurEdu\Users\UserEnums;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentRepositoryInterface;
use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\Users\Repository\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class CreateAssessmentUseCase implements CreateAssessmentUseCaseInterface
{

    private $assessmentRepo;
    private $user;
    private $userRepo;

    public function __construct(
        AssessmentRepositoryInterface $assessmentRepo,
        UserRepositoryInterface $userRepo
    ) {
        $this->assessmentRepo = $assessmentRepo;
        $this->userRepo = $userRepo;
        $this->user = Auth::guard('api')->user();
    }

    public function createAssessment($data): array
    {
        $validationErrors = $this->createAssessmentValidations($data);

        if ($validationErrors) {
            return $validationErrors;
        }
        $assessmentViewerTypes = $data->assessment_viewers_types->pluck('user_type')->toArray();

        $data['start_time'] = Carbon::parse($data->start_at)->format('H:i');
        $data['end_time'] =  Carbon::parse($data->end_at)->format('H:i');

        $data['start_at'] = Carbon::parse($data->start_at)->format('Y-m-d H:i');
        $data['end_at'] = Carbon::parse($data->end_at)->format('Y-m-d H:i');
        $data['school_account_id'] = $this->user->school_id;
        $data['assessor_type_is_general'] = isset($data->assessors) && $data->assessors->count() > 0 ? false:true;
        $data['assessee_type_is_general'] =isset($data->assessees) && $data->assessees->count() > 0 ? false:true;
        $data['assessment_viewer_type_is_general'] = $data->assessment_viewers_types->pluck('users')->flatten()->count() > 0 ? false:true;

            $assessment = $this->assessmentRepo->create($data->toArray());

        $assessment->resultViewerTypes()->delete();
        foreach ($assessmentViewerTypes as $viewerType) {
            $assessment->resultViewerTypes()->create(['user_type'=>$viewerType]);
        }

        $relationsData['assessors'] = isset($data->assessors) && $data->assessors->count() > 0 ?
            $data->assessors->pluck('id')->toArray() : [];
        $relationsData['assessees'] =  isset($data->assessees) && $data->assessees->count() > 0 ?
            $data->assessees->pluck('id')->toArray()
            :
            [];

        $relationsData['resultViewers'] = $data->assessment_viewers_types;

        AssignAssessmentRelationDataJob::dispatch($assessment, "assessors,assessees", $relationsData);

        $useCase['assessment'] = $assessment;
        $useCase['meta'] = [
            'message' => trans('assessment.assessment created')
        ];
        $useCase['status'] = 200;
        return $useCase;
    }

    protected function createAssessmentValidations($data)
    {

        $assessors = isset($data->assessors) ? $data->assessors->pluck('id')->toArray() : [];
        $assessees = isset($data->assessees) ? $data->assessees->pluck('id')->toArray() : [];

        $schoolUsers = $this->userRepo->getUser()
            ->where(function (Builder $query) {
                if (!is_null($this->user->school_id)) {
                    $query->whereHas("branch", function (Builder $builder) {
                        $builder->where("school_account_id", "=", $this->user->school_id);
                    })
                    ->orWhereHas("schoolSupervisor", function (Builder $builder) {
                        $builder->where("school_account_id", "=", $this->user->school_id);
                    })
                    ->orWhereHas("schoolLeader", function (Builder $builder) {
                        $builder->where("school_account_id", "=", $this->user->school_id);
                    })
                    ->orWhereHas('branches', function (Builder $schoolAccountBranch) {
                        $schoolAccountBranch->where("school_account_id", "=", $this->user->school_id);
                    })
                    ->orWhereHas('schoolAccount', function (Builder $builder) {
                        $builder->where('id', $this->user->school_id);
                    })
                    ->orWhere('school_id', $this->user->school_id);
                }
            });


        $assesseesTypeUsers = with(clone $schoolUsers)->where('type', $data->assessee_type)->pluck('id')->toArray();
        $assessorsTypeUsers = with(clone $schoolUsers)->where('type', $data->assessor_type)->pluck('id')->toArray();
        $assessmentViewersTypes = $data->assessment_viewers_types->pluck('user_type');
        if (count($assessees) > 0 && count(array_intersect($assessees, $assesseesTypeUsers)) !== count($assessees)) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('assessment.Invalid assessees');
            $useCase['title'] = 'Invalid assesses';
            return $useCase;
        }

        if (count($assessors) > 0 && count(array_intersect($assessors, $assessorsTypeUsers)) !== count($assessors)) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('assessment.Invalid assessors');
            $useCase['title'] = 'Invalid assessors';
            return $useCase;
        }
        foreach ($assessmentViewersTypes as $assessmentViewerType){
            if (empty($assessmentViewerType) || is_null($assessmentViewerType)){
                $useCase['status'] = 422;
                $useCase['detail'] = trans('assessment.assessment viewers can\'t be empty');
                $useCase['title'] = 'Assessment Viewers';
                return $useCase;

            }
        }
    }
}
