<?php


namespace App\OurEdu\Assessments\UseCases\AssessmentPointsRate;

use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\Assessments\Repositories\AssessmentRepository\AssessmentPointsRateRepositoryInterface;

class AssessmentPointRateUseCase implements AssessmentPointRateUseCaseInterface
{

    private $assessmentPointsRateRepo;

    public function __construct(
        AssessmentPointsRateRepositoryInterface $assessmentPointsRateRepository
    ) {
        $this->assessmentPointsRateRepo = $assessmentPointsRateRepository;
    }

    public function getAssessmentRates(Assessment $assessment)
    {
        return $this->assessmentPointsRateRepo->index($assessment->id);
    }

    public function createPointRates(Assessment $assessment, $data): array
    {

        $validationErrors = $this->validateCreation($assessment, $data);

        if ($validationErrors) {
            return $validationErrors;
        }

        //delete old rates
        $assessment->rates()->forceDelete();

        foreach ($data as $rate) {
            $rate->assessment_id = $assessment->id;
            $this->assessmentPointsRateRepo->create($rate->toArray());
        }

        $useCase['assessmentPointsRates'] = $assessment->rates;
        $useCase['meta'] = [
            'message' => trans('assessment.assessment_rates_created')
        ];
        $useCase['status'] = 200;
        return $useCase;
    }

    /**
     * Validate date of points rate
     * @param App\OurEdu\Assessments\Models\Assessment $assessment
     * @param array $data
     * @return array|null
     */
    private function validateCreation(Assessment $assessment, $data)
    {

        $validateRequest =  $this->validateRequest($data);
        if($validateRequest){
            return $validateRequest;
        }

        if ($assessment->published_at) {
            $useCase['status'] = 403;
            $useCase['detail'] = trans('assessment.published_assessment');
            $useCase['title'] = 'Assessment was published';
            return $useCase;
        }

        if (!$assessment->questions()->exists()) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('assessment.assessment_has_no_question');
            $useCase['title'] = 'No questions';
            return $useCase;
        }

        $flattenedData = $this->data_flatten($data);
        $error = $this->pointsInterfere($flattenedData);
        if ($error) {
            $useCase['status'] = 422;
            $useCase['detail'] = $error;
            $useCase['title'] = 'Points interfere';
            return $useCase;
        }

        if (max($flattenedData) != ($assessment->mark+$assessment->skipped_questions_grades)) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('assessment.max_points_not_equal');
            $useCase['title'] = 'Maximum points not equal';
            return $useCase;
        }
    }


    private function data_flatten($array): array
    {
        $flattened = [];
        foreach ($array as $key => $value) {
            array_push($flattened, $value->min_points);
            array_push($flattened, $value->max_points);
        }

        return $flattened;
    }

    private function pointsInterfere(array $data)
    {
        for ($i = 1; $i < count($data); $i += 2) {
            if ($data[0] != 0) return trans('assessment.must_start_with_zero');
            if ($i + 1 == count($data)) return;
            if (round($data[$i] + 1, 1) !=  $data[$i + 1]
                and round($data[$i] + 0.1, 1) !=  $data[$i + 1]
            )  return trans('assessment.points_rate_interfere');
        }
    }

    private function validateRequest($data)
    {
        $rates = [];

        foreach ($data as $rate) {
            $validateMinPoints = $this->validateMinPoints($rate);
            if($validateMinPoints){
                return $validateMinPoints;
            }
            $validateMaxPoints = $this->validateMaxPoints($rate);
            if($validateMaxPoints){
                return $validateMaxPoints;
            }
            $validateRate =  $this->validateRate($rate);
            if($validateRate){
                return $validateRate;
            }
            $rates [] = strtolower($rate->rate);
         }

        if(count(array_unique($rates)) != count($rates)){
          return  ['status' => 422 ,
                    'detail' => trans('validation.custom.rate.distinct'),
                    'title' => 'rate_not_distinct',
                  ];
        }
    }
    private function validateMinPoints($rate)
    {
        if (!isset($rate->min_points) ||  $rate->min_points == '' || count(explode(' ', $rate->min_points)) > 1) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('validation.custom.min_points.required');
            $useCase['title'] = 'min_points_required';
            return $useCase;
        }
        if (!is_numeric($rate->min_points)) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('validation.custom.min_points.integer_or_decimal');
            $useCase['title'] = 'min_points_not_number';
            return $useCase;
        }
        if(strlen(strrchr($rate->min_points , '.')) > 2 and is_float((float)$rate->min_points)){
            $useCase['status'] = 422;
            $useCase['detail'] = trans('validation.custom.min_points.decimal');
            $useCase['title'] = 'min_points_decimal_too_large';
            return $useCase;
        }
    }

    private function validateMaxPoints($rate)
    {
        if (!isset($rate->max_points) || $rate->max_points== '' || count(explode(' ', $rate->max_points)) > 1) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('validation.custom.max_points.required');
            $useCase['title'] = 'max_points_required';
            return $useCase;
        }
        if (!is_numeric($rate->max_points)) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('validation.custom.max_points.integer_or_decimal');
            $useCase['title'] = 'min_points_not_number';
            return $useCase;
        }
        if(strlen(strrchr($rate->max_points , '.')) > 2 and is_float((float)$rate->max_points)){
            $useCase['status'] = 422;
            $useCase['detail'] = trans('validation.custom.max_points.decimal');
            $useCase['title'] = 'max_points_decimal_too_large';
            return $useCase;
        }
        if ((int)$rate->max_points < (int)$rate->min_points) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('validation.custom.max_points.gt');
            $useCase['title'] = 'min_points_greater_than_max_points';
            return $useCase;
        }
    }
    private function validateRate($rate)
    {
        if (!isset($rate->rate) ||  $rate->rate == '' ||  ctype_space($rate->rate)) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('validation.custom.rate.required');
            $useCase['title'] = 'rate_required';
            return $useCase;
        }
        if (!is_string($rate->rate)) {
            $useCase['status'] = 422;
            $useCase['detail'] = trans('validation.custom.rate.string');
            $useCase['title'] = 'rate_not_string';
            return $useCase;
        }
    }
}
