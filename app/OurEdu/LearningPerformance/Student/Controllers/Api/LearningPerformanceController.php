<?php

namespace App\OurEdu\LearningPerformance\Student\Controllers\Api;

use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\LearningPerformance\LearningPerformance;
use App\OurEdu\LearningPerformance\Parent\Transformers\StudentPerformanceTransformer;
use App\OurEdu\LearningPerformance\Parent\Transformers\StudentSubjectPerformanceTransformer;
use App\OurEdu\LearningPerformance\UseCases\StudentOrderUseCase\StudentOrderUseCaseInterface;

class LearningPerformanceController extends BaseApiController
{

    private $StudentOrderUseCase;
    private $learningPerformance;

    public function __construct(
        StudentOrderUseCaseInterface $StudentOrderUseCase,
        LearningPerformance $learningPerformance
    )
    {
        $this->StudentOrderUseCase = $StudentOrderUseCase;
        $this->learningPerformance = $learningPerformance;
    }

    public function getStudentOrderInSubject($subjectId) {
        $student = auth()->user()->student;
        if(isset($student->id)){
            $res = $this->StudentOrderUseCase->getStudentOrderInSubject($subjectId);
            $order = array_search($student->id, array_keys($res));
        }
        return ($order + 1);

    }

    public function getStudentSubjectPerformance($studentId , $subjectId) {

        $this->learningPerformance->student_id = $studentId;
        $this->learningPerformance->subject_id = $subjectId;
        return $this->transformDataModInclude($this->learningPerformance, '', new StudentSubjectPerformanceTransformer(), ResourceTypesEnums::LEARNING_PERFORMANCE);

    }

    public function getStudentPerformance($studentId) {
        $this->learningPerformance->student_id = $studentId;
        return $this->transformDataModInclude($this->learningPerformance, '', new StudentPerformanceTransformer(), ResourceTypesEnums::LEARNING_PERFORMANCE);
    }
}
