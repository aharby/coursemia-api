<?php


namespace App\OurEdu\Assessments\UseCases\QuestionsUseCases\EssayUseCase;


use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\Assessments\Models\Questions\Essay\AssessmentEssayQuestion;
use App\OurEdu\Assessments\Repositories\AssessmentQuestions\AssessmentQuestionRepositoryInterface;
use App\OurEdu\Assessments\Repositories\AssessmentQuestions\EassyQuestions\AssessmentEssayQuestionRepository;
use Illuminate\Support\Str;

class EssayUseCase implements EssayUseCaseInterface
{

    /**
     * @var string
     */
    private string $slug;

    /**
     * @var AssessmentEssayQuestionRepository
     */
    private AssessmentEssayQuestionRepository $essayQuestionRepository;
    /**
     * @var AssessmentQuestionRepositoryInterface
     */
    private AssessmentQuestionRepositoryInterface $assessmentQuestionRepository;
    private float $questionGrade;
    private $category_id;

    /**
     * EssayUseCase constructor.
     * @param AssessmentEssayQuestionRepository $essayQuestionRepository
     * @param AssessmentQuestionRepositoryInterface $assessmentQuestionRepository
     */
    public function __construct(AssessmentEssayQuestionRepository $essayQuestionRepository, AssessmentQuestionRepositoryInterface $assessmentQuestionRepository)
    {
        $this->essayQuestionRepository = $essayQuestionRepository;
        $this->assessmentQuestionRepository = $assessmentQuestionRepository;
    }

    public function addQuestion(Assessment $assessment, $data)
    {
        $assessmentQuestions = $data->assessmentQuestions;
        $this->slug= $data->question_slug;
        $this->category_id = $data->category_id;
        $questions = $assessmentQuestions->questions;

        $error = $this->validate($questions,$data);
        if ($error) {
            return $error;
        }

        $multipleChoice = $this->createOrUpdateQuestions($assessment, $questions);

        return $multipleChoice;
    }

    private function createOrUpdateQuestions(Assessment $assessment, $questions)
    {
        foreach ($questions as $question) {

            $this->questionGrade = $question->grade ?? 0;

            $questionData = [
                'question' => $question->question ?? null,
                'slug' => $this->slug,
                'grade' => $this->questionGrade,
            ];
            $skipQuestion = isset($question->skip_question) ? $question->skip_question:false;

            if (Str::contains($question->id, 'new')) {
                $questionObj = $this->essayQuestionRepository->create($questionData);
                $assessmentQuestion = $this->updateOrCreateAssessmentQuestions($assessment, $questionObj, $skipQuestion);
                if ($skipQuestion) {
                    $assessment->skipped_questions_grades = $assessment->skipped_questions_grades + $this->questionGrade;
                } else {
                    $assessment->mark = $assessment->mark + $this->questionGrade;
                }

                $assessment->save();
            } else {
                $essayQuestion = $this->essayQuestionRepository->findOrFail($question->id);
                $questionObj = $this->essayQuestionRepository->update($essayQuestion, $questionData);
                $prevAssessmentQuestion = $questionObj->assessmentQuestion;
                $assessmentQuestion = $this->updateOrCreateAssessmentQuestions($assessment,$questionObj, $skipQuestion);
                if ($prevAssessmentQuestion->skip_question) {
                    $assessment->skipped_questions_grades = $assessment->skipped_questions_grades - $prevAssessmentQuestion->question_grade;
                } else {
                    $assessment->mark = $assessment->mark - $prevAssessmentQuestion->question_grade;
                }

                if ($skipQuestion) {
                    $assessment->skipped_questions_grades = $assessment->skipped_questions_grades + $this->questionGrade;
                } else {
                    $assessment->mark = $assessment->mark + $this->questionGrade;
                }
                $assessment->save();
            }

        }
        return $assessmentQuestion;
    }

    private function updateOrCreateAssessmentQuestions(Assessment $assessment, AssessmentEssayQuestion $question,bool $skipQuestion)
    {
        $data = [
            'question_type' => AssessmentEssayQuestion::class,
            'question_id' => $question->id,
            'school_account_branch_id' => auth()->user()->branch->id ?? null,
            'school_account_id' => auth()->user()->branch->schoolAccount->id ?? null,
            'assessment_id'=>$assessment->id,
            'question_grade'=>$this->questionGrade,
            'skip_question' => $skipQuestion,
            'category_id'=> $this->category_id
        ];

        if ($this->slug) {
            $data['slug'] = $this->slug;
        }

        $question =  $this->assessmentQuestionRepository->updateOrCreate($assessment->id,$question->id,$data);
        return $question;
    }


    public function validate($questions,$data)
    {
        $errors = [];
        foreach ($questions as $question){

            if(!property_exists($question,'question') || $question->question ==='' || ctype_space($question->question)){
                $errors['errors'][] = [
                    'status' => 422,
                    'title' => 'question_is_missing',
                    'detail' => trans('assessment.required', ['field' => trans('assessment.question_header')])
                ];
            }
        }

        return $errors;
    }

}
