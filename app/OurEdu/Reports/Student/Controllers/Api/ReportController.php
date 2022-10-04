<?php

namespace App\OurEdu\Reports\Student\Controllers\Api;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Exceptions\OurEduErrorException;
use App\Exceptions\ErrorResponseException;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use Swis\JsonApi\Client\Interfaces\ParserInterface;
use App\OurEdu\Reports\Student\Requests\ReportRequest;
use App\OurEdu\Reports\Repository\ReportRepositoryInterface;
use App\OurEdu\Reports\Student\Transformers\ReportTransformer;
use App\OurEdu\Reports\UseCase\StudentReportUseCase\StudentReportUseCaseInterface;

class ReportController extends BaseApiController
{
    private $repository;
    private $parserInterface;
    private $studentReportUseCase;

    public function __construct(
        ReportRepositoryInterface $reportRepository,
        ParserInterface $parserInterface,
        StudentReportUseCaseInterface $studentReportUseCase
    ) {
        $this->middleware('auth:api');

        $this->repository = $reportRepository;
        $this->parserInterface = $parserInterface;
        $this->studentReportUseCase = $studentReportUseCase;
        $this->user = Auth::guard('api')->user();
    }

    public function postCreateReport(ReportRequest $request, $subjectId, $reportType, $id)
    {
        if (Cache::has("{$this->user->id}_reported")) {
            throw new ErrorResponseException(trans("api.Please wait :time minutes", ["time"=>env('REPORT_CACHE_TIME', 1)]));
        }

        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();
        try {
            $create = [];
            $create['report'] = $data->report;
            $create['subjectId'] = $subjectId;
            $create['reportable_id'] = $id;
            $create['reportable_type'] = $reportType;
            $create['student_id'] = auth()->user()->student->id;
            $create = $this->studentReportUseCase->studentReport($create);
            if ($create['code'] == 200) {
                $meta = ['message' => trans('api.Thanks for reporting')];

                return $this->transformDataModInclude(
                    $create['report'],
                    '',
                    new ReportTransformer(),
                    ResourceTypesEnums::REPORT,
                    $meta
                );
            } else {
                $error = [
                    'status' => $create['code'],
                    'title' => $create['title'],
                    'detail' => $create['detail']
                ];
                return formatErrorValidation($error, $create['code']);
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }
}
