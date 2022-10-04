<?php


namespace App\OurEdu\Assessments\UseCases\CloneAssessment;


use App\Exceptions\OurEduErrorException;
use App\OurEdu\Assessments\Enums\QuestionTypesEnums;
use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\Assessments\Models\AssessmentQuestion;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentRepositoryInterface;
use App\OurEdu\Assessments\UseCases\UpdateAssessmentUseCase\UpdateAssessmentUseCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Swis\JsonApi\Client\Item;

class CloneAssessmentUseCase implements CloneAssessmentUseCaseInterface
{
    /**
     * @var AssessmentRepositoryInterface
     */
    private $assessmentRepo;


    private  $cloneAssessementQuestionId;

    /**
     * CloneAssessmentUseCase constructor.
     * @param AssessmentRepositoryInterface $assessmentRepo
     */
    public function __construct(AssessmentRepositoryInterface $assessmentRepo)
    {
        $this->assessmentRepo = $assessmentRepo;
    }

    public function cloneAssessment(Assessment $assessment, Item $data)
    {
        try
        {
            DB::beginTransaction();

            //replicate Assessment and take id
            $cloneAssessment = $assessment->replicate();

            $cloneAssessment->published_at = null;
            $cloneAssessment->average_score = 0;
            $cloneAssessment->published_before = false;
            $cloneAssessment->start_at = $data->start_at;
            $cloneAssessment->end_at = $data->end_at;
            $cloneAssessment->average_score = 0;
            $cloneAssessment->total_assesses_count = 0;
            $cloneAssessment->assessed_assesses_count = 0;

            $cloneAssessment->save();
            $coloneAssessmentId = $cloneAssessment->id;

            $assessmentViewerTypes = $assessment->resultViewerTypes()->get();
            foreach ($assessmentViewerTypes as $viewerType) {
                $cloneAssessment->resultViewerTypes()->create(['user_type'=>$viewerType->user_type]);
            }

            //replicate assessees and assessor and assessment viewer

            if (isset($assessment->assessors)) {
                $assessors = $assessment->assessors->pluck('id')->toArray();
                $this->assessmentRepo->saveAssessmentAssessors($cloneAssessment, $assessors);
            }

            if (isset($assessment->assessees)) {
                $assessees = $assessment->assessees->pluck('id')->toArray();
                $this->assessmentRepo->saveAssessmentAssessees($cloneAssessment, $assessees);
            }

            if (isset($assessment->resultViewers)) {
                $assessment_viewers = $assessment->resultViewers()->pluck('user_id')->toArray();
                $this->assessmentRepo->saveAssessmentViewers($cloneAssessment, $assessment_viewers);
            }


            //replicate points rates of assessment
            $assessmentPointRates = $assessment->rates;
            if (isset($assessmentPointRates)) {
                foreach ($assessmentPointRates as $assessmentPointRate) {
                    $cloneAssessmentPointRate = $assessmentPointRate->replicate();
                    $cloneAssessmentPointRate->assessment_id = $coloneAssessmentId;
                    $cloneAssessmentPointRate->save();
                }
            }
            $assessmentCategories = $assessment->categories;
            if ($assessmentCategories->count()) {
                foreach ($assessmentCategories as $category) {
                    $cloneCategory = $category->replicate();
                    $cloneCategory->assessment_id = $coloneAssessmentId;
                    $cloneCategory->save();
                }
            }
            //replicate Assessment Questions
            $assessmentQuestions = $assessment->questions()->get();
            $useCase = $this->replicatAssessmentQuestionsWithOptions($assessmentQuestions , $cloneAssessment , $assessment);
             
            DB::commit();
        
            if($useCase){
            return $useCase;

            }
            
            $useCase['assessment'] = $cloneAssessment;
            $useCase['meta'] = [
                'message' => trans('assessment.assessment is cloned')
            ];
            $useCase['status'] = 200;
            return $useCase;

       }
        catch (\Throwable $e)
        {
            DB::rollBack();
            throw new OurEduErrorException($e->getMessage());
        }

    }


    public function replicatAssessmentQuestionsWithOptions($assessmentQuestions , $assessment , $oldAssessment)
    {
        $oldcategories = $oldAssessment->categories->pluck('id')->sortBy('id')->toArray();
        $newcategories = $assessment->categories->pluck('id')->sortBy('id')->toArray();

        foreach ($assessmentQuestions as $assessmentQuestion)
        {
            $category = null;

            if((!empty($oldcategories) and !empty($newcategories)) and !is_null($assessmentQuestion->category_id)) {
                $categoryIndex = array_search($assessmentQuestion->category_id, $oldcategories);
                $category = $newcategories[$categoryIndex];
            }
            $question = $assessmentQuestion->question()->first();
            if(!$question)
            {
                $useCase['status'] = 422;
                $useCase['detail'] = trans('assessment.this assessment does not have question');
                $useCase['title'] = 'this assessment does not have question';
                return $useCase;
            }
            $cloneAllAssessementQuestion = $question->replicate();
            $cloneAllAssessementQuestion->save();
            $this->cloneAssessementQuestionId = $cloneAllAssessementQuestion->id;


            if ($assessmentQuestion->slug === QuestionTypesEnums::SINGLE_CHOICE || $assessmentQuestion->slug === QuestionTypesEnums::MULTI_CHOICE)
            {
                $assessmentQuestionOptions = $question->options()->get();

                foreach ($assessmentQuestionOptions as $assessmentQuestionOption)
                {
                    $cloneAssessmentQuestionOption = $assessmentQuestionOption->replicate();
                    $cloneAssessmentQuestionOption->assessment_mcq_id = $this->cloneAssessementQuestionId;
                    $cloneAssessmentQuestionOption->save();
                }
            }
            if ($assessmentQuestion->slug === QuestionTypesEnums::SATISFICATION_RATING || $assessmentQuestion->slug === QuestionTypesEnums::STAR_RATING || $assessmentQuestion->slug === QuestionTypesEnums::SCALE_RATING)
            {
                $assessmentQuestionOptions = $question->options()->get();

                foreach ($assessmentQuestionOptions as $assessmentQuestionOption)
                {
                    $cloneAssessmentQuestionOption = $assessmentQuestionOption->replicate();

                    $cloneAssessmentQuestionOption->assessment_rating_question_id = $this->cloneAssessementQuestionId;
                    $cloneAssessmentQuestionOption->save();
                }
            }
            if ($assessmentQuestion->slug === QuestionTypesEnums::MATRIX)
            {
                $assessmentQuestionMatrixRows = $question->rows()->get();
                foreach ($assessmentQuestionMatrixRows as $assessmentQuestionMatrixRow)
                {
                    $cloneAssessmentQuestionMatrixRow = $assessmentQuestionMatrixRow->replicate();
                    $cloneAssessmentQuestionMatrixRow->assess_data_id = $this->cloneAssessementQuestionId;
                    $cloneAssessmentQuestionMatrixRow->save();
                }

                $assessmentQuestionMatrixColumns = $question->columns()->get();
                foreach ($assessmentQuestionMatrixColumns as $assessmentQuestionMatrixColumn)
                {
                    $cloneAssessmentQuestionMatrixColumns = $assessmentQuestionMatrixColumn->replicate();
                    $cloneAssessmentQuestionMatrixColumns->assess_data_id = $this->cloneAssessementQuestionId;
                    $cloneAssessmentQuestionMatrixColumns->save();
                }
            }
            $cloneAssessmentQuestion = $assessmentQuestion->replicate();
            $cloneAssessmentQuestion->assessment_id = $assessment->id ;
            $cloneAssessmentQuestion->question_id = $this->cloneAssessementQuestionId ;
            $cloneAssessmentQuestion->category_id = $category ;
            $cloneAssessmentQuestion->save();
        }

    }

}
