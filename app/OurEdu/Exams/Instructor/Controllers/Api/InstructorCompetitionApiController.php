<?php

namespace App\OurEdu\Exams\Instructor\Controllers\Api;

use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Exams\Events\CompetitionEvents\StudentStartedCompetition;
use App\OurEdu\Exams\Instructor\Requests\GenerateInstructorExamRequest;
use App\OurEdu\Exams\Instructor\Transformers\InstructorCompetitionTransformer;
use App\OurEdu\Exams\Repository\Exam\ExamRepositoryInterface;
use App\OurEdu\Exams\UseCases\FinishExamUseCase\FinishExamUseCaseInterface;
use App\OurEdu\Exams\UseCases\PrepareExamQuestion\GenerateExamUseCase\GenerateExamUseCaseInterface;
use App\OurEdu\Exams\UseCases\StartExamUseCase\StartExamUseCaseInterface;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Swis\JsonApi\Client\Interfaces\ParserInterface;

class InstructorCompetitionApiController extends BaseApiController
{
    private $parserInterface;
    private $generateExamUseCaseInterface;
    private $examRepository;
    private $startExamUseCase;
    private $finishExamUseCase;

    public function __construct(
        ParserInterface $parserInterface,
        GenerateExamUseCaseInterface $generateExamUseCaseInterface,
        ExamRepositoryInterface $examRepository,
        StartExamUseCaseInterface $startExamUseCase,
        FinishExamUseCaseInterface $finishExamUseCase
    ) {
        $this->parserInterface = $parserInterface;
        $this->generateExamUseCaseInterface = $generateExamUseCaseInterface;
        $this->examRepository = $examRepository;
        $this->startExamUseCase = $startExamUseCase;
        $this->finishExamUseCase = $finishExamUseCase;
    }

    public function generateInstructorCompetition(GenerateInstructorExamRequest $request , VCRSession $VcrSession)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();

        DB::beginTransaction();
        $numberOfQuestions = $data->number_of_questions;
        $difficultyLevel = $data->difficulty_level;
        $subjectFormatSubjectIds = $data->subject_format_subject_ids;
        $subjectId = $VcrSession->subject_id;

        $instructorId = auth()->user()->id;
        $exam = $this->generateExamUseCaseInterface->generateInstructorCompetition($instructorId, $subjectId, $subjectFormatSubjectIds, $numberOfQuestions, $difficultyLevel,$VcrSession);

        if (isset($exam['error'])) {
            return response()->json([
                'meta' => [
                    'message' =>  $exam['message']
                ]
            ]);
        }

        DB::commit();

        $meta = ['message' => trans('api.Competition generated')];

        return $this->transformDataModInclude($exam, '',
            new InstructorCompetitionTransformer(),
            ResourceTypesEnums::INSTRUCTOR_COMPETITION, $meta);
    }

    public function startInstructorCompetition($competitionId)
    {
        $usecase = $this->startExamUseCase->startInstructorCompetition($competitionId);
        if ($usecase['status'] == 200) {
            return response()->json([
                'meta' => [
                    'message' =>  $usecase['message']
                ]
            ]);
        } else {
            return formatErrorValidation($usecase);
        }
    }

    public function finishInstructorCompetition($competitionId)
    {
        $usecase = $this->finishExamUseCase->finishInstructorCompetition($competitionId);
        if ($usecase['status'] == 200) {
            return response()->json([
                'meta' => [
                    'message' =>  $usecase['message']
                ]
            ]);
        } else {
            return formatErrorValidation($usecase);
        }
    }
}
