<?php


namespace App\OurEdu\Assessments\UseCases\QuestionsUseCases\MultipleChoiceUseCase;

use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\Options\Option;
use App\OurEdu\Assessments\Enums\QuestionTypesEnums;
use App\OurEdu\Assessments\Models\Questions\MultipleChoice\AssessmentMultipleChoiceQuestion;
use App\OurEdu\Assessments\Repositories\AssessmentQuestions\MultipleChoice\MultipleChoiceRepository;
use App\OurEdu\Assessments\Repositories\AssessmentQuestions\MultipleChoice\MultipleChoiceRepositoryInterface;
use App\OurEdu\Assessments\Repositories\AssessmentQuestions\AssessmentQuestionRepositoryInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class MultipleChoiceUseCase implements MultipleChoiceUseCaseInterface
{
    /**
     * @var MultipleChoiceRepositoryInterface
     */
    private $multipleChoiceRepository;
    /**
     * @var AssessmentQuestionRepositoryInterface
     */
    private $assessmentQuestionRepo;

    private $multipleChoiceType;
    private $category_id;

    /**
     * MultipleChoiceUseCase constructor.
     * @param MultipleChoiceRepositoryInterface $multipleChoiceRepository
     * @param AssessmentQuestionRepositoryInterface $assessmentQuestionRepo
     */
    public function __construct(
        MultipleChoiceRepositoryInterface $multipleChoiceRepository,
        AssessmentQuestionRepositoryInterface $assessmentQuestionRepo
    )
    {
        $this->multipleChoiceRepository = $multipleChoiceRepository;
        $this->assessmentQuestionRepo = $assessmentQuestionRepo;
    }

    public function addQuestion(Assessment $assessment, $data)
    {
        $assessmentQuestions = $data->assessmentQuestions;

        $this->multipleChoiceType = $data->question_slug;
        $this->category_id = $data->category_id;

        $questions = $assessmentQuestions->questions;
        if ($data->question_slug == QuestionTypesEnums::SINGLE_CHOICE) {
            $error = $this->validateMCQSingleChoiceQuestions($questions,$data);
            if ($error) {
                return $error;
            }
        }

        if ($data->question_slug == QuestionTypesEnums::MULTI_CHOICE) {
            $error = $this->validateMultipleChoicesQuestions($questions,$data);
            if ($error) {
                return $error;
            }
        }
        $multipleChoice = $this->createOrUpdateQuestions($assessment, $questions,$data);

        return $multipleChoice;
    }

    private function validateMCQSingleChoiceQuestions($questions,$data)
    {
        $errors = null;
        foreach ($questions as $question) {

            if(!property_exists($question,'question') || $question->question ==='' || ctype_space($question->question)){
                $errors['errors'][] = [
                    'status' => 422,
                    'title' => 'question header_is_missing',
                    'detail' => trans('assessment.required', ['field' => trans('assessment.question_header')])
                ];
            }

            if (count($question->options) < 2) {
                $errors['errors'][] = [
                    'status' => 422,
                    'title' => 'there has to be 2 options or more',
                    'detail' => trans('assessment.there has to be 2 or more options')
                ];
            }
            foreach ($question->options as $key => $option) {
                if (!property_exists($option, 'option') || $option->option === '' || ctype_space($option->option)) {
                    $errors['errors'][] = [
                        'status' => 422,
                        'title' => 'option_is_missing',
                        'detail' => trans('assessment.required', ['num' => $key + 1, 'field' => trans('assessment.option')])
                    ];
                }
                if (!property_exists($option, 'grade') || $option->grade === '' || !is_numeric($option->grade) || $option->grade < 0) {
                    $errors['errors'][] = [
                        'status' => 422,
                        'title' => 'grade_is_missing_or_invalid',
                        'detail' => trans('assessment.grade_required_numeric', ['num' => $key + 1])
                    ];
                }
            }
        }

        return $errors;
    }

    private function createOrUpdateQuestions(
        Assessment $assessment,
        $questions
    ){
        foreach ($questions as $question) {
            $questionId = $question->id;
            $this->questionGrade = max(collect($question->options)->pluck('grade')->toArray());
            $questionData = [
                'question' => $question->question ?? null,
                'url' => $question->url ?? '',
                'slug'=>$this->multipleChoiceType
            ];
            $skipQuestion = isset($question->skip_question) ? $question->skip_question:false;
            if (Str::contains($questionId, 'new')) {
                $questionObj = $this->multipleChoiceRepository->createQuestion($questionData);
                $assessmentQuestion = $this->updateOrCreateAssessmentQuestions($assessment, $questionObj, $skipQuestion);
                if ($skipQuestion) {
                    $assessment->skipped_questions_grades = $assessment->skipped_questions_grades + $this->questionGrade;
                } else {
                    $assessment->mark = $assessment->mark + $this->questionGrade;
                }

                $assessment->save();
            } else {
                $questionObj = $this->multipleChoiceRepository->updateQuestion($questionId, $questionData);
                $prevAssessmentQuestion = $questionObj->assessmentQuestion;
                $assessmentQuestion = $this->updateOrCreateAssessmentQuestions($assessment, $questionObj, $skipQuestion);
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
            $this->createOrUpdateOptions($this->multipleChoiceRepository, $questionObj->id, $question);
        }
        return $assessmentQuestion;
    }

    private function createOrUpdateOptions(MultipleChoiceRepository $multipleChoiceRepo, $questionId, $question)
    {
        $options = $question->options ?? [];

        $optionsDataMultiple = [];
        $this->deleteOptions($multipleChoiceRepo, $questionId, $options);

        foreach ($options as $option) {

            $optionId = $option->id;
            $optionData = [
                'answer' => $option->option,
                'grade' => $option->grade,
                'assessment_mcq_id' => $questionId
            ];

            if (Str::contains($optionId, 'new')) {
                $optionsDataMultiple[] = $optionData;
            } else {
                $multipleChoiceRepo->updateOption($optionId, $optionData);
            }
        }

        if (count($optionsDataMultiple) > 0) {
            $insert = $multipleChoiceRepo->insertMultipleOptions($optionsDataMultiple);
        }
    }

    private function deleteOptions(MultipleChoiceRepository $multipleChoiceRepo, $questionId, $options)
    {
        $newIds = Arr::pluck($options, 'id');
        $oldQuestionsIds = $multipleChoiceRepo->getQuestionOptionsIds($questionId);

        $deleteIds = array_diff($oldQuestionsIds, $newIds);
        $multipleChoiceRepo->deleteOptions($questionId, $deleteIds);

    }


    private function updateOrCreateAssessmentQuestions(Assessment $assessment, AssessmentMultipleChoiceQuestion $question, bool $skipQuestion)
    {
        $data = [
            'question_type' => AssessmentMultipleChoiceQuestion::class,
            'question_id' => $question->id,
            'school_account_branch_id' => auth()->user()->branch->id ?? null,
            'school_account_id' => auth()->user()->branch->schoolAccount->id ?? null,
            'assessment_id'=>$assessment->id,
            'question_grade'=>$this->questionGrade,
            'skip_question' => $skipQuestion,
            'category_id' => $this->category_id
        ];

        if ($this->multipleChoiceType) {
            $data['slug'] = $this->multipleChoiceType;
        }

        $question =  $this->assessmentQuestionRepo->updateOrCreate($assessment->id,$question->id,$data);
        return $question;
    }


    public function validateMultipleChoicesQuestions($questions,$data)
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

            if (count($question->options) < 2) {
                $errors['errors'][] = [
                    'status' => 422,
                    'title' => 'there has to be 2 options',
                    'detail' => trans('assessment.there has to be 2 or more options')
                ];
            }

            foreach ($question->options as $key => $option) {
                if (!property_exists($option, 'option') || $option->option === '' || ctype_space($option->option)) {
                    $errors['errors'][] = [
                        'status' => 422,
                        'title' => 'option_is_missing',
                        'detail' => trans('assessment.required', ['num' => $key + 1, 'field' => trans('assessment.option')])
                    ];
                }
                if (!property_exists($option, 'grade') || $option->grade === '' || !is_numeric($option->grade) || $option->grade < 0 ) {
                    $errors['errors'][] = [
                        'status' => 422,
                        'title' => 'grade_is_missing_or_invalid',
                        'detail' => trans('assessment.grade_required_numeric', ['num' => $key + 1])
                    ];
                }
            }
        }

        return $errors;

    }

}
