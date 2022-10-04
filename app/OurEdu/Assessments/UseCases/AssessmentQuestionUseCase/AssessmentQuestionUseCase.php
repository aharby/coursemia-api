<?php


namespace App\OurEdu\Assessments\UseCases\AssessmentQuestionUseCase;

use App\OurEdu\Assessments\Enums\QuestionTypesEnums;
use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\Assessments\Models\AssessmentQuestion;
use App\OurEdu\Assessments\UseCases\CloneAssessment\CloneAssessmentUseCaseInterface;
use App\OurEdu\Assessments\UseCases\QuestionsUseCases\EssayUseCase\EssayUseCaseInterface;
use App\OurEdu\Assessments\UseCases\QuestionsUseCases\MultipleChoiceUseCase\MultipleChoiceUseCaseInterface;
use App\OurEdu\Assessments\Repositories\AssessmentQuestions\AssessmentQuestionRepositoryInterface;
use App\OurEdu\Assessments\UseCases\QuestionsUseCases\SatisficationRatingUseCase\SatisficationRatingUseCase;
use App\OurEdu\Assessments\UseCases\QuestionsUseCases\ScaleRatingUseCase\ScaleRatingUseCase;
use App\OurEdu\Assessments\UseCases\QuestionsUseCases\StarRatingUseCase\StarRatingUseCaseInterface;
use App\OurEdu\Assessments\UseCases\QuestionsUseCases\ScaleRatingUseCase\ScaleRatingUseCaseInterface;
use App\OurEdu\Assessments\UseCases\QuestionsUseCases\MatrixUseCase\MatrixUseCaseInterface;
use Illuminate\Support\Facades\DB;
use App\Exceptions\OurEduErrorException;
class AssessmentQuestionUseCase implements AssessmentQuestionUseCaseInterface
{
    /**
     * @var AssessmentQuestionRepositoryInterface
     */
    private $assessmentQuestionRepo;

    /**
     * @var MultipleChoiceUseCaseInterface
     */
    private $multipleChoiceUseCase;

    /**
     * @var StarRatingUseCaseInterface
     */
    private $starRatingUseCase;

    /**
     * @var ScaleRatingUseCaseInterface
     */
    private $scaleRatingUseCase;


    /**
     * @var MatrixUseCaseInterface
     */
    private $matrixUseCase;
    /**
     * @var EssayUseCaseInterface
     */
    private EssayUseCaseInterface $essayUseCase;

    /**
     * AssessmentQuestionUseCase constructor.
     * @param AssessmentQuestionRepositoryInterface $assessmentQuestionRepo
     * @param MultipleChoiceUseCaseInterface $multipleChoiceUseCase
     * @param StarRatingUseCaseInterface $starRatingUseCase
     * @param ScaleRatingUseCaseInterface $scaleRatingUseCase
     * @param MatrixUseCaseInterface $matrixUseCase
     * @param SatisficationRatingUseCase $satisficationRatingUseCase
     * @param EssayUseCaseInterface $essayUseCase
     */
    public function __construct(
        AssessmentQuestionRepositoryInterface $assessmentQuestionRepo,
        MultipleChoiceUseCaseInterface $multipleChoiceUseCase,
        StarRatingUseCaseInterface $starRatingUseCase,
        ScaleRatingUseCaseInterface $scaleRatingUseCase,
        MatrixUseCaseInterface $matrixUseCase,
        SatisficationRatingUseCase  $satisficationRatingUseCase,
        EssayUseCaseInterface $essayUseCase,
        CloneAssessmentUseCaseInterface $cloneAssessmentUseCase
    ) {
        $this->assessmentQuestionRepo = $assessmentQuestionRepo;
        $this->multipleChoiceUseCase = $multipleChoiceUseCase;
        $this->starRatingUseCase = $starRatingUseCase;
        $this->scaleRatingUseCase = $scaleRatingUseCase;
        $this->matrixUseCase = $matrixUseCase;
        $this->satisficationRatingUseCase = $satisficationRatingUseCase;
        $this->essayUseCase = $essayUseCase;
        $this->cloneAssessmentUseCase = $cloneAssessmentUseCase;
    }

    public function addQuestion(Assessment $assessment, $data)
    {
        $validationErrors = $this->validateAddQuestions($assessment, $data);
        if ($validationErrors) {
            return $validationErrors;
        }


        switch ($data->question_slug) {
            case QuestionTypesEnums::MULTI_CHOICE:
            case QuestionTypesEnums::SINGLE_CHOICE:
                return $this->multipleChoiceUseCase->addQuestion($assessment, $data);
                break;

            case QuestionTypesEnums::STAR_RATING:
                return $this->starRatingUseCase->addQuestion($assessment, $data);
                break;

            case QuestionTypesEnums::SCALE_RATING:
                return $this->scaleRatingUseCase->addQuestion($assessment, $data);
                break;

            case QuestionTypesEnums::MATRIX:
                return $this->matrixUseCase->addQuestion($assessment, $data);
                break;

            case QuestionTypesEnums::SATISFICATION_RATING:
                return $this->satisficationRatingUseCase->addQuestion($assessment, $data);
                break;

            case QuestionTypesEnums::ESSAY_QUESTION:
                return $this->essayUseCase->addQuestion($assessment, $data);
                break;

            default:
                $errors['errors'][] = [
                    'status' => 422,
                    'title' => "Question Type not Supported",
                    'detail' => "Question Type not Supported",
                ];

                return $errors;
        }
    }


    private function validateAddQuestions(Assessment $assessment, $data)
    {
        if ($assessment->created_by !== auth()->user()->id) {
            unauthorize();
        }

        if ($assessment->published_at) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('assessment.cannot add any questions assessment already published');
            $useCase['title'] = 'cant edit published assessment';
            $errors['errors'][] = $useCase;

            return $errors;
        }

        if (!is_null($assessment->end_at)) {
            if (now() > $assessment->end_at) {
                $useCase['status'] = 422;
                $useCase['detail'] = trans('assessment.assessment time passed');
                $useCase['title'] = 'assessment time passed';
                $errors['errors'][] = $useCase;
                return $errors;
            }
        }
        if (!$assessment->categories->count()) {
                $useCase['status'] = 422;
                $useCase['detail'] = trans('assessment.cannot add any questions there are no categories');
                $useCase['title'] = 'cannot add any questions there are no categories';
                $errors['errors'][] = $useCase;
                return $errors;
        }
    }

    public function deleteQuestion(Assessment $assessment, AssessmentQuestion $assessmentQuestion)
    {
        if ($assessmentQuestion->assessment_id != $assessment->id) {
            $useCase['status'] = 405;
            $useCase['detail'] = trans("assessment.question_not_found");
            $useCase['title'] = 'Question Not Found';
            return $useCase;
        }

        if ($assessment->published_at) {
            $useCase['status'] = 405;
            $useCase['detail'] = trans("assessment.cannot delete any questions assessment already published");
            $useCase['title'] = 'Assessment published';
            return $useCase;
        }

        $this->assessmentQuestionRepo->delete($assessment, $assessmentQuestion);

        if ($assessmentQuestion->skip_question) {
            $assessment->skipped_questions_grades = $assessment->skipped_questions_grades - $assessmentQuestion->question_grade;
        } else {
            $assessment->mark = $assessment->mark - $assessmentQuestion->question_grade;
        }
        $assessment->save();

        $useCase['status'] = 200;
        return $useCase;
    }

    public function cloneQuestion(AssessmentQuestion $assessmentQuestion, Assessment $assessment)
    {
        try
        {
        DB::beginTransaction();
       $useCase = $this->cloneAssessmentUseCase->replicatAssessmentQuestionsWithOptions(
            $assessment->questions()->where('id',$assessmentQuestion->id)->get(),
            $assessment,
            $assessment
        );
        if ($useCase) {
           return $useCase;
        }
        $useCase['assessmentQuestion'] = $assessment->questions()->latest()->first();
    
        if($useCase['assessmentQuestion']){
        $skipQuestion = isset($useCase['assessmentQuestion']->skip_question) ? $useCase['assessmentQuestion']->skip_question:false;
        if ($skipQuestion) {
            $assessment->skipped_questions_grades = $useCase['assessmentQuestion']->question_grade + $assessment->skipped_questions_grades;
        } else {
            $assessment->mark =$useCase['assessmentQuestion']->question_grade + $assessment->mark;
        }
        $assessment->save();

        }
        DB::commit();
        $useCase['status'] = 200;

        return $useCase;

    }catch (\Throwable $e)
    {
        DB::rollBack();
        throw new OurEduErrorException($e->getMessage());
    }
}
}
