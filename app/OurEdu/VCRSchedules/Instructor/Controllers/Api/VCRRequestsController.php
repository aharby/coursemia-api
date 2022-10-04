<?php

namespace App\OurEdu\VCRSchedules\Instructor\Controllers\Api;

use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Exams\Repository\Exam\ExamRepositoryInterface;
use App\OurEdu\Subjects\Repository\SubjectRepositoryInterface;
use App\OurEdu\Users\Repository\UserRepositoryInterface;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\VCRSchedules\Instructor\Middlewares\Api\AcceptRequestMiddleware;
use App\OurEdu\VCRSchedules\Instructor\Middlewares\Api\StudentReportMiddleware;
use App\OurEdu\VCRSchedules\Instructor\Transformers\ExamTransformer;
use App\OurEdu\VCRSchedules\Instructor\Transformers\UserTransformer;
use App\OurEdu\VCRSchedules\Instructor\Transformers\VCRRequestsTransformer;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSchedules\Repository\VCRRequestRepositoryInterface;
use App\OurEdu\VCRSchedules\Repository\VCRScheduleRepositoryInterface;
use App\OurEdu\VCRSchedules\UseCases\VCRRequestUseCase\VCRRequestUseCaseInterface;
use App\OurEdu\VCRSchedules\UseCases\VCRSessionUseCase\VCRSessionUseCaseInterface;

class VCRRequestsController extends BaseApiController
{
    private $vcrScheduleRepository;
    private $userRepository;
    private $subjectRepository;
    private $VCRRequestUseCase;
    private $examRepository;
    private $VCRRequestRepository;
    private $VCRSessionUseCase;

    public function __construct(VCRScheduleRepositoryInterface $vcrScheduleRepository,
                                SubjectRepositoryInterface $subjectRepository,
                                UserRepositoryInterface $userRepository,
                                VCRRequestUseCaseInterface $VCRRequestUseCase,
                                ExamRepositoryInterface $examRepository,
                                VCRRequestRepositoryInterface $VCRRequestRepository,
                                VCRSessionUseCaseInterface $VCRSessionUseCase
    )
    {
        $this->vcrScheduleRepository = $vcrScheduleRepository;
        $this->subjectRepository = $subjectRepository;
        $this->userRepository = $userRepository;
        $this->VCRRequestUseCase = $VCRRequestUseCase;
        $this->examRepository = $examRepository;
        $this->VCRRequestRepository = $VCRRequestRepository;
        $this->VCRSessionUseCase = $VCRSessionUseCase;
        $this->middleware(AcceptRequestMiddleware::class)->only(['acceptVcrRequest']);
        $this->middleware(StudentReportMiddleware::class)->only(['getStudentReport']);


    }

    public function getVcrRequests()
    {

        $instructor = auth()->user();

        $requests = $this->VCRRequestRepository->getInstructorRequests($instructor->id);
        return $this->transformDataModInclude($requests, 'students', new VCRRequestsTransformer(), ResourceTypesEnums::VCR_REQUEST);

    }

    public function getStudentReport($requestId)
    {

        $request = $this->VCRRequestRepository->findOrFail($requestId);
        $exam = $this->examRepository->findOrFail($request->exam_id);
        $include = 'feedback,actions,questions';
        $param['request'] = $request;
        return $this->transformDataModInclude($exam, $include, new ExamTransformer($param), ResourceTypesEnums::Exam);
    }

    public function acceptVcrRequest($requestId)
    {

        $this->VCRRequestUseCase->acceptRequest($requestId);

        $returnArr = $this->VCRSessionUseCase->createSession($requestId);


        $meta = [
            'message' => $returnArr['title'],
            'details' => $returnArr['detail'],
            'web_url' => $returnArr['detail'],
            'api_url' => $returnArr['detail'],
        ];

        return response()->json(['meta' => $meta], $returnArr['status']);
    }

    public function VCRPresenceStudents(VCRSession $VCRSession)
    {
        $presence = User::query()
            ->whereHas("VCRSessionsPresence", function ($VCRSessionsPresence) use ($VCRSession) {
                $VCRSessionsPresence->where("vcr_session_id", "=", $VCRSession->id);
            })
            ->where("type", "=", UserEnums::STUDENT_TYPE)
            ->get();
        return $this->transformDataMod($presence, new UserTransformer(), ResourceTypesEnums::USER);
    }

}

