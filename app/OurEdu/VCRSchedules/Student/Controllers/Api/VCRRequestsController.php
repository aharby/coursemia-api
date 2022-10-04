<?php

namespace App\OurEdu\VCRSchedules\Student\Controllers\Api;

use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Payments\Enums\PaymentEnums;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Subjects\Repository\SubjectRepositoryInterface;
use App\OurEdu\Users\Repository\UserRepositoryInterface;
use App\OurEdu\VCRSchedules\Repository\VCRScheduleRepositoryInterface;
use App\OurEdu\VCRSchedules\Student\Transformers\VCRScheduleTransformer;
use App\OurEdu\VCRSchedules\UseCases\VCRRequestUseCase\VCRRequestUseCaseInterface;

class VCRRequestsController extends BaseApiController
{
    private $vcrScheduleRepository;
    private $userRepository;
    private $subjectRepository;
    private $VCRRequestUseCase;

    public function __construct(
        VCRScheduleRepositoryInterface $vcrScheduleRepository,
        SubjectRepositoryInterface $subjectRepository,
        UserRepositoryInterface $userRepository,
        VCRRequestUseCaseInterface $VCRRequestUseCase
    ) {
        $this->vcrScheduleRepository = $vcrScheduleRepository;
        $this->subjectRepository = $subjectRepository;
        $this->userRepository = $userRepository;
        $this->VCRRequestUseCase = $VCRRequestUseCase;
    }

    public function postRequestVcr($vcr, $day, $exam = null)
    {
        $vcr = $this->vcrScheduleRepository->findOrFail($vcr);
        $day = $this->vcrScheduleRepository->getWorkingDay($vcr->id, $day);
        $student = auth()->user()->student;
        $paymentMethod = request()->paymentMethod ?? PaymentEnums::WALLET;

        $returnArr = $this->VCRRequestUseCase->request($vcr, $day, $student, $exam, $paymentMethod);

        if (isset($returnArr['status']) and $returnArr['status'] != 200) {
            return formatErrorValidation($returnArr, $returnArr['status']);
        }

        $meta = [
            'message' => $returnArr['title'],
            'details' => $returnArr['detail'],
            'session_id' => $returnArr['session_id'] ?? '',
            'web_url' => $returnArr['web_url'] ?? '',
            'api_url' => $returnArr['api_url'] ?? '',
            'vcr_request_id' => $returnArr['vcr_request_id'] ?? '',
            'instructor_url' => $returnArr['instructor_url'] ?? '',
        ];

        return response()->json(['meta' => $meta], $returnArr['status']);
    }

    public function availableRequestsBySubject()
    {
        $this->setFilters();

        $meta = [
            'filters' => formatFiltersForApi($this->filters),
        ];

        $vcrs = $this->vcrScheduleRepository->getAllVcrSpotInstructors($this->filters);

        return $this->transformDataModInclude($vcrs, 'actions, subject, instructor',new VCRScheduleTransformer(), ResourceTypesEnums::VCR_REQUEST,$meta);
    }

    public function setFilters()
    {
        $student = auth()->user()->student;

        $subjects = Subject::query()->where('country_id', auth()->user()->country_id)->where(
            'educational_system_id',
            $student->educational_system_id
        )->where(
            'academical_years_id',
            $student->academical_year_id
        )->where('is_top_qudrat', true)
            ->pluck('name', 'id')->toArray();

        $this->filters[] = [
            'name' => 'subject_id',
            'type' => 'select',
            'data' => $subjects,
            'trans' => false,
            'value' => request()->get('subject_id'),
        ];
    }

}
