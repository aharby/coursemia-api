<?php

namespace Database\Seeders;

use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\Assessments\Models\AssessmentAssessor;
use App\OurEdu\Assessments\Models\AssessmentUser;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentUsersRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Seeder;

class UpdateAssessmentTotalMarkSeeder extends Seeder
{
    private AssessmentUsersRepositoryInterface $assessmentUsersRepository;

    public function __construct(AssessmentUsersRepositoryInterface $assessmentUsersRepository)
    {

        $this->assessmentUsersRepository = $assessmentUsersRepository;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $assessments = Assessment::query()
            ->with("assessmentUsers")
            ->get();

        foreach ($assessments as $assessment) {
            AssessmentUser::query()
                ->where("assessment_id", "=", $assessment->id)
                ->where("total_mark", "=", 0)
                ->update(['total_mark' => $assessment->mark]);

            $average_total_mark= AssessmentUser::query()
                ->where("assessment_id", "=", $assessment->id)
                ->average("total_mark") ;

            $assessment->average_total_mark = $average_total_mark > 0 ? number_format($average_total_mark, "2", ".","") : $assessment->mark;

            $assessment->save();

            foreach ($assessment->assessmentUsers as $assessmentUser) {
                $userAssessmentsAverageScore = AssessmentUser::query()
                    ->where('assessment_id', $assessment->id)
                    ->where("user_id", $assessmentUser->user_id)
                    ->finished()
                    ->average('score');

                $averageTotalMark = AssessmentUser::query()
                    ->where('assessment_id', $assessment->id)
                    ->where("user_id", $assessmentUser->user_id)
                    ->finished()
                    ->average('total_mark');

                AssessmentAssessor::query()
                    ->where("assessment_id", "=", $assessment->id)
                    ->where("user_id", "=",$assessmentUser->user_id)
                    ->update(
                        [
                            "average_score" => $userAssessmentsAverageScore ?? 0,
                            "average_total_mark" => $averageTotalMark ?? 0
                        ]
                    );
            }


            foreach ($assessment->resultViewers as $viewer) {

                try {
                    $assessors = $this->assessmentUsersRepository->getAssessmentAssessors($assessment, false, $viewer);
                } catch (\Exception $exception) {
                    continue;
                }

                foreach ($assessors as $assessor) {

                    $assessmentUsers = $this->assessmentUsersRepository->getAssessedUsersOfAssessor(
                        $assessment,
                        $assessor->user_id,
                        $viewer
                    );

                    $avgScore = $assessmentUsers
                        ->where('assessee_id', '!=', $viewer->id)
                        ->average('score');

                    $avgTotalMark = $assessmentUsers
                        ->where('assessee_id', '!=', $viewer->id)
                        ->average('total_mark');

                    $viewer->assessorViewerAvgScores()->updateOrCreate(
                        [
                            'assessment_id' => $assessment->id,
                            'assessor_id' => $assessor->user_id,
                            'viewer_id' => $viewer->id,
                        ],
                        [
                            'average_score' => $avgScore ?? 0,
                            'average_total_mark' => $avgTotalMark ?? 0
                        ]
                    );
                }
            }
        }
    }
}
