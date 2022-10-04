<?php

namespace App\OurEdu\LearningPerformance\StudentTeacher\Controllers\Api;

use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Exams\Repository\Exam\ExamRepositoryInterface;
use App\OurEdu\LearningPerformance\LearningPerformance;
use App\OurEdu\LearningPerformance\StudentTeacher\Middlewares\Api\StudentTeacherInRelationMiddleware;
use App\OurEdu\LearningPerformance\StudentTeacher\Middlewares\Api\StudentTeacherSeeSubjectMiddleware;
use App\OurEdu\LearningPerformance\StudentTeacher\Transformers\StudentAllSubjectsPerformanceTransformer;
use App\OurEdu\LearningPerformance\StudentTeacher\Transformers\ExamPerformanceTransformer;
use App\OurEdu\LearningPerformance\StudentTeacher\Transformers\StudentPerformanceTransformer;
use App\OurEdu\LearningPerformance\StudentTeacher\Transformers\StudentSubjectPerformanceTransformer;
use App\OurEdu\LearningPerformance\UseCases\StudentOrderUseCase\StudentOrderUseCaseInterface;
use App\OurEdu\LearningPerformance\UseCases\StudentSuccessRateUseCase\StudentSuccessRateUseCaseInterface;
use App\OurEdu\Subjects\Repository\SubjectRepositoryInterface;
use App\OurEdu\Users\Repository\StudentRepositoryInterface;

class LearningPerformanceController extends BaseApiController
{

    private $StudentOrderUseCase;
    private $studentSuccessRateUseCase;
    private $learningPerformance;
    private $studentRepository;
    private $subjectRepository;
    private $examRepository;

    public function __construct(
        StudentOrderUseCaseInterface $StudentOrderUseCase,
        StudentRepositoryInterface $studentRepository,
        SubjectRepositoryInterface $subjectRepository,
        ExamRepositoryInterface $examRepository,
        LearningPerformance $learningPerformance,
        StudentSuccessRateUseCaseInterface $studentSuccessRateUseCase
    )
    {
        $this->studentRepository = $studentRepository;
        $this->subjectRepository = $subjectRepository;
        $this->examRepository = $examRepository;
        $this->studentSuccessRateUseCase = $studentSuccessRateUseCase;
        $this->StudentOrderUseCase = $StudentOrderUseCase;
        $this->learningPerformance = $learningPerformance;
        $this->middleware(StudentTeacherInRelationMiddleware::class)
            ->only(['getStudentAllSubjectsPerformance', 'getStudentSubjectPerformance']);

        $this->middleware(StudentTeacherSeeSubjectMiddleware::class)
            ->only(['getStudentOrderInSubject', 'getStudentSubjectPerformance']);

    }

    private function getStudentOrderInSubject($studentId, $subjectId) {
        if(isset($studentId)){
            $res = $this->StudentOrderUseCase->getStudentOrderInSubject($subjectId);
            $order = array_search($studentId, array_keys($res));
        }
        return ($order + 1);
    }


    public function getStudentPerformance($studentId) {
        $this->learningPerformance->student_id = $studentId;
        return $this->transformDataModInclude($this->learningPerformance, '', new StudentPerformanceTransformer(), ResourceTypesEnums::LEARNING_PERFORMANCE);
    }

    public function getStudentSubjectPerformance($studentId, $subjectId)
    {
        $this->learningPerformance->student = $this->studentRepository->findOrFail($studentId);
        $this->learningPerformance->subject = $this->subjectRepository->findOrFail($subjectId);

        $this->learningPerformance->student_order =
            $this->getStudentOrderInSubject($studentId, $subjectId);
        $this->learningPerformance->success_rate =
            $this->studentSuccessRateUseCase
                ->getSuccessRateOnSubject($studentId, $subjectId);

        // speed_percentage order according to all students
        $this->learningPerformance->solving_speed_percentage_order =
            $this->studentSuccessRateUseCase
                ->getStudentSpeedOrderOfSolvingExams($studentId, $subjectId);

        // subject progress percentage order according to all students
        $this->learningPerformance->subject_progress_percentage_order =
            $this->studentSuccessRateUseCase
                ->getSubjectProgressPercentage($studentId, $subjectId);

        // exams count order according to all students
        $this->learningPerformance->exams_count_order =
            $this->studentSuccessRateUseCase
                ->getExamsCountsOrder($studentId, $subjectId);

        return $this->transformDataModInclude($this->learningPerformance, '',
            new StudentSubjectPerformanceTransformer(),
            ResourceTypesEnums::LEARNING_PERFORMANCE);

    }

    public function getExamPerformance($examId)
    {
        $exam = $this->examRepository->findOrFail($examId);
        return $this->transformDataModInclude($exam, '',
            new ExamPerformanceTransformer(),
            ResourceTypesEnums::EXAM_PERFORMANCE);
    }

    public function getStudentAllSubjectsPerformance($studentId)
    {
        $studentObj = $this->studentRepository->findOrFail($studentId);
        $this->learningPerformance->student = $studentObj;

        return $this->transformDataModInclude($this->learningPerformance, '',
            new StudentAllSubjectsPerformanceTransformer(),
            ResourceTypesEnums::LEARNING_PERFORMANCE);

    }
}
