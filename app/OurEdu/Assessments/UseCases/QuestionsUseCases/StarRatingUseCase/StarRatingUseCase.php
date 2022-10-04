<?php


namespace App\OurEdu\Assessments\UseCases\QuestionsUseCases\StarRatingUseCase;

use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\Options\Option;
use App\OurEdu\Assessments\Models\Questions\Rating\AssissmentRatingQuestion;
use App\OurEdu\Assessments\Repositories\AssessmentQuestions\RatingQuestion\RatingQuestionRepository;
use App\OurEdu\Assessments\Repositories\AssessmentQuestions\RatingQuestion\RatingQuestionRepositoryInterface;
use App\OurEdu\Assessments\Repositories\AssessmentQuestions\AssessmentQuestionRepositoryInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class StarRatingUseCase implements StarRatingUseCaseInterface
{
    /**
     * @var RatingQuestionRepositoryInterface
     */
    private $ratingQuestionRepository;
    /**
     * @var AssessmentQuestionRepositoryInterface
     */
    private $assessmentQuestionRepo;

    private $starRatingType;
    private $category_id;

    /**
     * StarRatingUseCase constructor.
     * @param RatingQuestionRepositoryInterface $ratingQuestionRepository
     * @param AssessmentQuestionRepositoryInterface $assessmentQuestionRepo
     */
    public function __construct(
        RatingQuestionRepositoryInterface $ratingQuestionRepository,
        AssessmentQuestionRepositoryInterface $assessmentQuestionRepo
    )
    {
        $this->ratingQuestionRepository = $ratingQuestionRepository;
        $this->assessmentQuestionRepo = $assessmentQuestionRepo;
    }

    public function addQuestion(Assessment $assessment, $data)
    {
        $assessmentQuestions = $data->assessmentQuestions;
        $this->starRatingType = $data->question_slug;
        $this->category_id = $data->category_id;

        $questions = $assessmentQuestions->questions;
        $error = $this->validateStarRatingQuestions($questions,$data);
        if ($error) {
            return $error;
        }

        $starRatingQuestion = $this->createOrUpdateQuestions($assessment, $questions,$data);

        return $starRatingQuestion;
    }

    private function validateStarRatingQuestions($questions,$data)
    {
        $errors = null;
        foreach ($questions as $question) {

            if(!property_exists($question,'question') || $question->question ==='' || ctype_space($question->question)){
                $errors['errors'][] = [
                    'status' => 422,
                    'title' => 'question_is_missing',
                    'detail' => trans('assessment.required', ['field' => trans('assessment.question_header')])
                ];
            }

            if($question->star_number > 5 || $question->star_number <1){
                $errors['errors'][] = [
                    'status' => 422,
                    'title' => 'star number must be  greater than 1 and less than 5',
                    'detail' => trans('assessment.star number must be  greater than 1 and less than 5')
                ];
            }

            if (count($question->options) != $question->star_number) {
                $errors['errors'][] = [
                    'status' => 422,
                    'title' => 'options must be equal  to star number',
                    'detail' => trans('assessment.options must be equal  to star number')
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
                if (!property_exists($option, 'order') || $option->order === ''  || !is_numeric($option->order) || $option->order > $question->star_number){
                    $errors['errors'][] = [
                        'status' => 422,
                        'title' => 'option_order_is_missing_or_invalid',
                        'detail' => trans('assessment.order_is_missing_or_invalid', ['num' => $key + 1])
                    ];
                }
                if(!property_exists($option, 'grade') || $option->grade === '' || !is_numeric($option->grade) || $option->grade < 0) {
                    $errors['errors'][] = [
                        'status' => 422,
                        'title' => 'grade_is_missing_or_invalid',
                        'detail' => trans('assessment.grade_required_numeric', ['num' => $key + 1])
                    ];
                }
            }

            $optionOrders = collect($question->options)->pluck('order')->toArray();
            if(count($optionOrders) > count(array_unique(($optionOrders)))){
                $errors['errors'][] = [
                    'status' => 422,
                    'title' => 'options should have different orders',
                    'detail' => trans('assessment.options should have different orders')
                ];
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
            $this->questionGrade = collect($question->options)->sum('grade');
            $questionData = [
                'question' => $question->question ?? null,
                'url' => $question->url ?? '',
                'slug'=>$this->starRatingType,
                'direction'=>$question->direction
            ];

            $skipQuestion = isset($question->skip_question) ? $question->skip_question:false;

            if (Str::contains($questionId, 'new')) {
                $questionObj = $this->ratingQuestionRepository->createQuestion($questionData);
                $assessmentQuestion = $this->updateOrCreateAssessmentQuestions($assessment, $questionObj, $skipQuestion);
                if ($skipQuestion) {
                    $assessment->skipped_questions_grades = $assessment->skipped_questions_grades + $this->questionGrade;
                } else {
                    $assessment->mark = $assessment->mark + $this->questionGrade;
                }

                $assessment->save();
            } else {
                $questionObj = $this->ratingQuestionRepository->updateQuestion($questionId, $questionData);
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
            $this->createOrUpdateOptions($this->ratingQuestionRepository, $questionObj->id, $question);
        }
        return $assessmentQuestion;
    }

    private function createOrUpdateOptions(RatingQuestionRepository $ratingQuestionRepository, $questionId, $question)
    {
        $options = $question->options ?? [];

        $optionsDataMultiple = [];
        $this->deleteOptions($ratingQuestionRepository, $questionId, $options);

        foreach ($options as $option) {

            $optionId = $option->id;
            $optionData = [
                'answer' => $option->option,
                'grade' => $option->grade,
                'assessment_rating_question_id' => $questionId,
                'order'=> $option->order
            ];

            if (Str::contains($optionId, 'new')) {
                $optionsDataMultiple[] = $optionData;
            } else {
                $ratingQuestionRepository->updateOption($optionId, $optionData);
            }
        }

        if (count($optionsDataMultiple) > 0) {
            $insert = $ratingQuestionRepository->insertMultipleOptions($optionsDataMultiple);
        }
    }

    private function deleteOptions(RatingQuestionRepository $ratingQuestionRepository, $questionId, $options)
    {
        $newIds = Arr::pluck($options, 'id');
        $oldQuestionsIds = $ratingQuestionRepository->getQuestionOptionsIds($questionId);

        $deleteIds = array_diff($oldQuestionsIds, $newIds);
        $ratingQuestionRepository->deleteOptions($questionId, $deleteIds);

    }


    private function updateOrCreateAssessmentQuestions(Assessment $assessment, AssissmentRatingQuestion $question, bool $skipQuestion)
    {
        $data = [
            'question_type' => AssissmentRatingQuestion::class,
            'question_id' => $question->id,
            'school_account_branch_id' => auth()->user()->branch->id ?? null,
            'school_account_id' => auth()->user()->branch->schoolAccount->id ?? null,
            'assessment_id' => $assessment->id,
            'question_grade'=>$this->questionGrade,
            'skip_question' => $skipQuestion,
            'category_id'=> $this->category_id
        ];

        if ($this->starRatingType) {
            $data['slug'] = $this->starRatingType;
        }

        $question =  $this->assessmentQuestionRepo->updateOrCreate($assessment->id,$question->id,$data);
        return $question;
    }
}
