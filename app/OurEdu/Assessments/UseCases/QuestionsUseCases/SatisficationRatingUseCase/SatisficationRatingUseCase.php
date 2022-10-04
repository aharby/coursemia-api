<?php


namespace App\OurEdu\Assessments\UseCases\QuestionsUseCases\SatisficationRatingUseCase;

use App\OurEdu\Assessments\Enums\SatisficationTypesEnums;
use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\Options\Option;
use App\OurEdu\Assessments\Models\Questions\Rating\AssissmentRatingQuestion;
use App\OurEdu\Assessments\Repositories\AssessmentQuestions\RatingQuestion\RatingQuestionRepository;
use App\OurEdu\Assessments\Repositories\AssessmentQuestions\RatingQuestion\RatingQuestionRepositoryInterface;
use App\OurEdu\Assessments\Repositories\AssessmentQuestions\AssessmentQuestionRepositoryInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class SatisficationRatingUseCase implements SatisficationRatingUseCaseInterface
{
    /**
     * @var RatingQuestionRepositoryInterface
     */
    private $ratingQuestionRepository;
    /**
     * @var AssessmentQuestionRepositoryInterface
     */
    private $assessmentQuestionRepo;

    private $SatisficationRatingType;
    private $category_id;

    /**
     * ScaleRatingUseCase constructor.
     * @param RatingQuestionRepositoryInterface $ratingQuestionRepository
     * @param AssessmentQuestionRepositoryInterface $assessmentQuestionRepo
     */
    public function __construct(
        RatingQuestionRepositoryInterface $ratingQuestionRepository,
        AssessmentQuestionRepositoryInterface $assessmentQuestionRepo
    ) {
        $this->ratingQuestionRepository = $ratingQuestionRepository;
        $this->assessmentQuestionRepo = $assessmentQuestionRepo;
    }

    public function addQuestion(Assessment $assessment, $data)
    {
        $this->SatisficationRatingType = $data->question_slug;
        $questions = $data->assessmentQuestions->questions;
        $this->category_id = $data->category_id;

        $error = $this->validateSatisficationRatingQuestions($questions,$data);
        if ($error) {
            return $error;
        }

        $SatisficationRatingQuestion = $this->createOrUpdateQuestions($assessment, $questions, $data);

        return $SatisficationRatingQuestion;
    }

    private function validateSatisficationRatingQuestions($questions,$data)
    {
        $errors = null;
        foreach ($questions as $question) {

            if(!property_exists($question,'question') || $question->question ==='' || ctype_space($question->question)){
                $errors['errors'][] = [
                    'status' => 422,
                    'title' => 'question_is_missing',
                    'detail' => trans('assessment.required', ['field' => trans('assessment.question')])
                ];
            }


            if (count($question->options) != 5) {
                $errors['errors'][] = [
                    'status' => 422,
                    'title' => 'satisfication options must be equal  five',
                    'detail' => trans('assessment.options_satisfication_equality')
                ];
            }

            foreach ($question->options as $key => $option) {
                if (!property_exists($option, 'satisfication_slug') || $option->satisfication_slug === '' ) {
                    $errors['errors'][] = [
                        'status' => 422,
                        'title' => 'satisfication_slug_is_missing',
                        'detail' => trans('assessment.required', ['num' => $key + 1, 'field' => trans('assessment.option')])
                    ];
                }

                if (!property_exists($option, 'option') || $option->option === '' || ctype_space($option->option)) {
                    $errors['errors'][] = [
                        'status' => 422,
                        'title' => 'option_is_missing',
                        'detail' => trans('assessment.required', ['num' => $key + 1, 'field' => trans('assessment.option')])
                    ];
                }
                if (!property_exists($option, 'order') || $option->order === ''  || !is_numeric($option->order) ||  !is_integer($option->order) || $option->order > 5 || $option->order < 1 ) {
                    $errors['errors'][] = [
                        'status' => 422,
                        'title' => 'option_order_is_missing_or_invalid',
                        'detail' => trans('assessment.order_is_missing_or_invalid', ['num' => $key + 1])
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

            $optionSatisficationsSlug =array_unique(collect($question->options)->pluck('satisfication_slug')->toArray());
            $alllSatisficationOptins = SatisficationTypesEnums::satisficationTypes() ;

            $checkAllValuesOfSatisficationIsThere = ( count( $optionSatisficationsSlug ) == count(  $alllSatisficationOptins) && !array_diff( $optionSatisficationsSlug, $alllSatisficationOptins ) );

            if(!$checkAllValuesOfSatisficationIsThere){
                $errors['errors'][] = [
                    'status' => 422,
                    'title' => 'options should have all five satisfaction slug [excellent , very_good , good , bad , very_bad]',
                    'detail' => trans('assessment.satisfaction_level_required')
                ];
            }

            $optionOrders = collect($question->options)->pluck('order')->toArray();
            if (count($optionOrders) > count(array_unique(($optionOrders)))) {
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
    ) {
        foreach ($questions as $question) {
            $questionId = $question->id;
            $this->questionGrade = max(collect($question->options)->pluck('grade')->toArray());

            $questionData = [
                'question' => $question->question,
                'url' => $question->url ?? '',
                'slug' => $this->SatisficationRatingType,
                'direction' => $question->direction ?? ''
            ];
            $skipQuestion = isset($question->skip_question) ? $question->skip_question:false;

            if (Str::contains($questionId, 'new')) {
                $questionObj = $this->ratingQuestionRepository->createQuestion($questionData);
                $assessmentQuestion = $this->updateOrCreateAssessmentQuestions($assessment, $questionObj, $skipQuestion);;

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
                'order' => $option->order,
                'satisfication_slug' => $option->satisfication_slug
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

        if ($this->SatisficationRatingType) {
            $data['slug'] = $this->SatisficationRatingType;
        }

        $question =  $this->assessmentQuestionRepo->updateOrCreate($assessment->id,$question->id,$data);
        return $question;
    }
}
