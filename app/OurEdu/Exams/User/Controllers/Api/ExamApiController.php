<?php

namespace App\OurEdu\Exams\User\Controllers\Api;

use App\Exceptions\OurEduErrorException;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Exams\Repository\Exam\ExamRepositoryInterface;
use App\OurEdu\VCRSchedules\Repository\VCRSessionRepository;
use App\OurEdu\VCRSchedules\UseCases\VCRNotificationUseCase\VCRNotificationUseCaseInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Swis\JsonApi\Client\Interfaces\ParserInterface;
use App\OurEdu\Exams\User\Requests\GenerateExamRequest;
use App\OurEdu\Exams\User\Transformers\ExamTransformer;
use App\OurEdu\Exams\UseCases\PrepareExamQuestion\GenerateExamWithoutStudentUseCase;

class ExamApiController extends BaseApiController
{
    private $parserInterface;
    private $generateExamUseCaseInterface;
    private $examRepository;
    private $notificationUseCase;
    private $VCRSessionRepository;


    public function __construct(
        ParserInterface $parserInterface,
        GenerateExamWithoutStudentUseCase $generateExamUseCaseInterface,
        ExamRepositoryInterface $examRepository,
        VCRNotificationUseCaseInterface $notificationUseCase,
        VCRSessionRepository $VCRSessionRepository
    ) {
        $this->parserInterface = $parserInterface;
        $this->generateExamUseCaseInterface = $generateExamUseCaseInterface;
        $this->examRepository = $examRepository;
        $this->notificationUseCase = $notificationUseCase;
        $this->VCRSessionRepository = $VCRSessionRepository;
    }

    public function generateExam(GenerateExamRequest $request)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();
        try {

            DB::beginTransaction();
            $numberOfQuestions = $data->number_of_questions;
            $difficultyLevel = $data->difficulty_level;
            $subjectFormatSubjectIds = $data->subject_format_subject_ids;
            $subjectId = $data->subject_id;
            $allSessionID = request('session_id' , null);

            $exam = $this->generateExamUseCaseInterface->generateExam(null, $subjectId, $subjectFormatSubjectIds, $numberOfQuestions, $difficultyLevel , $allSessionID);

            if (isset($exam['error'])) {

                return formatErrorValidation($exam);
            }

            DB::commit();

            if (isset($allSessionID)) {
                $this->notificationUseCase->examGeneratedNotification($allSessionID, $exam->id);
            }
            $meta = ['message' => trans('api.Exam generated')];

            return $this->transformDataModInclude($exam, '', new ExamTransformer(), ResourceTypesEnums::Exam, $meta);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);
            throw $e;
//            throw new OurEduErrorException($e->getMessage());
        }
    }

    public function viewExam($examId)
    {
        try {
            $exam = $this->examRepository->findOrFail($examId);
            $include = 'questions,feedback';

            $params['retake_exam'] = false;

            $params['actions'] = false;

            return $this->transformDataModInclude($exam, $include, new \App\OurEdu\Exams\Student\Transformers\ExamTransformer($params), ResourceTypesEnums::Exam);
        } catch (\Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }

}
