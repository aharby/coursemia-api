<?php

namespace App\OurEdu\Assessments\UseCases\UpdateAssessmentUseCase;

use App\OurEdu\Assessments\AssessmentManager\AssignAssessmentRelationDataJob;
use App\OurEdu\Assessments\Jobs\FinishAssessmentsJob;
use App\OurEdu\Assessments\Jobs\UpdateTotalAssesseCountJob;
use Illuminate\Support\Facades\Auth;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentRepositoryInterface;
use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\Users\Repository\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class UpdateAssessmentUseCase implements UpdateAssessmentUseCaseInterface
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

    public function editAssessment($assessmentId, $data): array
    {
        $assessment = $this->assessmentRepo->findOrFail($assessmentId);

        $validationErrors = $this->editAssessmentValidations($assessment, $data);
        if ($validationErrors) {
            return $validationErrors;
        }
        $assessmentViewerTypes = $data->assessment_viewers_types->pluck('user_type')->toArray();

        $data['start_time'] = Carbon::parse($data->start_at)->format('H:i');
        $data['end_time'] =  Carbon::parse($data->end_at)->format('H:i');

        $data['start_at'] = Carbon::parse($data->start_at)->format('Y-m-d H:i');
        $data['end_at'] = Carbon::parse($data->end_at)->format('Y-m-d H:i');
        $data['assessor_type_is_general'] = isset($data->assessors) && $data->assessors->count() > 0 ? false:true;
        $data['assessee_type_is_general'] =isset($data->assessees) && $data->assessees->count() > 0 ? false:true;
        $data['assessment_viewer_type_is_general'] = $data->assessment_viewers_types->pluck('users')->flatten()->count() > 0 ? false:true;

        $this->assessmentRepo->setAssessment($assessment)->update($data->toArray());

        $assessment =  $this->assessmentRepo->getAssessment();

        $assessment->resultViewerTypes()->delete();
        foreach ($assessmentViewerTypes as $viewerType) {
            $assessment->resultViewerTypes()->create(['user_type' => $viewerType]);
        }

        $relationsData['assessors'] = isset($data->assessors) && $data->assessors->count() > 0 ? $data->assessors->pluck('id')->toArray() : [];
        $relationsData['assessees'] =  isset($data->assessees) && $data->assessees->count() > 0 ? $data->assessees->pluck('id')->toArray() : [];

        $relationsData['resultViewers'] = $data->assessment_viewers_types;
        AssignAssessmentRelationDataJob::dispatch($assessment, "assessors,assessees", $relationsData);

        $useCase['assessment'] = $assessment;
        $useCase['meta'] = [
            'message' => trans('app.Update successfully')
        ];
        $useCase['status'] = 200;
        return $useCase;
    }

    private function editAssessmentValidations(Assessment $assessment, $data)
    {
        if($assessment->published_at or ($assessment->published_before and $assessment->start_at <= now())) {
            $useCase['status'] = 403;
            $useCase['detail'] = trans('assessment.edit_not_allowed');
            $useCase['title'] = 'Edit not allowed';
            return $useCase;
        }

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
    }


    public function publishAssessment(Assessment $assessment)
    {
        $validationErrors = $this->validateAssessmentPublish($assessment);
        if ($validationErrors) {
            return $validationErrors;
        }

        if ((new Carbon($assessment->end))->isFuture()) {
            FinishAssessmentsJob::dispatch($assessment)->delay((new Carbon($assessment->end_at))->addMinute());
        }

        $this->assessmentRepo->setAssessment($assessment)->update(["published_at" => Carbon::now(),
                                                                    'published_before' => true]);

        UpdateTotalAssesseCountJob::dispatch($assessment);

        $useCase["status"] = 200;

        return $useCase;
    }


    public function unpublishAssessment(Assessment $assessment)
    {
        $this->assessmentRepo->setAssessment($assessment)->update(["published_at" => null]);
        $useCase["status"] = 200;
        return $useCase;
    }

    //delete all assessments related data
    public function delete(Assessment $assessment): void
    {
        foreach (Assessment::$relations_to_cascade as $relation) {
            $assessment->{$relation}()->delete();
        }

        $assessment->delete();
    }

    private function validateAssessmentPublish($assessment)
    {
        if ($assessment->published_at) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('assessment.assessment Already Published');
            $useCase['title'] = 'assessment already published';
            return $useCase;
        }

        $questionCount = $assessment->questions()->count();

        if ($questionCount < 1) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('assessment.assessment not have any Question, you have to add the questions before publishing');
            $useCase['title'] = 'assessment Not have questions';
            return $useCase;
        }

        if (!$assessment->rates()->exists()) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('assessment.has_no_rates');
            $useCase['title'] = 'assessment has no rates';
            return $useCase;
        }

        if ($assessment->rates()->max('max_points') != ($assessment->mark + $assessment->skipped_questions_grades)) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('assessment.max_points_not_equal');
            $useCase['title'] = 'Maximum points not equal';
            return $useCase;
        }
    }
}
