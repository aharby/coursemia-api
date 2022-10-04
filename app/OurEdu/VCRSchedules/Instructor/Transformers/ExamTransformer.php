<?php


namespace App\OurEdu\VCRSchedules\Instructor\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\DynamicLinksEnum;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Exams\Student\Transformers\QuestionTransformer;
use App\OurEdu\Users\Auth\Enum\TokenNameEnum;
use App\OurEdu\Users\Auth\TokenManager\TokenManagerInterface;
use App\OurEdu\VCRSchedules\UseCases\VCRSessionUseCase\VCRSessionUseCase;
use App\OurEdu\VCRSchedules\UseCases\VCRSessionUseCase\VCRSessionUseCaseInterface;
use App\OurEdu\VCRSchedules\VCRRequestStatusEnum;
use App\OurEdu\VCRSessions\General\Enums\VCRProvidersEnum;
use App\OurEdu\VCRSessions\General\Enums\VCRSessionsTypeEnum;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use League\Fractal\TransformerAbstract;

class ExamTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];
    protected array $availableIncludes = [
        'questions',
//        'recommendation',
        'actions',
        'feedback',

    ];
    protected $useCaseData;
    private $params;
    private $timeToSolveFlag = false;
    /**
     * @var TokenManagerInterface
     */
    private $tokenManager;
    /**
     * @var VCRSessionUseCaseInterface
     */
    private VCRSessionUseCaseInterface $VCRSessionUseCase;
    private string $meeting_type = VCRProvidersEnum::AGORA;

    public function __construct($params = [])
    {
        $this->params = $params;
        // if current route is start exam (student refresh exam), then calculate time to solve based on remaining time
        if (Route::currentRouteName() == 'api.student.exams.post.startExam') {
            $this->timeToSolveFlag = true;
        }
        $this->VCRSessionUseCase = app(VCRSessionUseCase::class);

        $this->tokenManager = app(TokenManagerInterface::class);
    }

    public function transform(Exam $exam)
    {
        $timeToSolve = round($exam->time_to_solve);
        // in case of refresh, time to solve updated to remaining time
        if ($this->timeToSolveFlag) {
            // get difference in seconds then subtract those seconds from time to solve
            $now = Carbon::now();
            $examStartTime = Carbon::parse($exam->start_time);
            $timeToSolve -= $now->diffInSeconds($examStartTime);
        }

        $transformerData = [
            'id' => (int)$exam->id,
            'title' => (string) $exam->title,
            'questions_numbers' => $exam->questions_number,
            'number_of_pages' => $exam->questions_number,
            'student' => $exam->student->user->first_name,
            'difficulty_level' => trans('difficulty_levels.'.$exam->difficulty_level),
            'subject_format_subject_id' => $exam->subject_format_subject_id,
            'subject_id' => $exam->subject_id,

            'start_time' => $exam->start_time,
            'finished_time' => $exam->finished_time,
            'is_finished' => (bool)$exam->is_finished,
            'is_started' => (bool)$exam->is_started,
            'time_to_solve' => $timeToSolve,
            'student_time_to_solve' => round($exam->student_time_to_solve),
        ];

        $vcrSession = $this->params['request']->vcrSession;

        if ($vcrSession) {
            $transformerData["vcr_session_id"] = $vcrSession->id;
            $this->meeting_type = $this->VCRSessionUseCase->getSessionMeetingProvider($vcrSession);
        }

        $transformerData["meeting_type"] = $this->meeting_type;

        return $transformerData;
    }

    public function includeActions(Exam $exam)
    {
        $actions = [];
        if ($this->params['request']->status != VCRRequestStatusEnum::REJECTED) {
            $vcrSessionId = $this->params['request']->vcrSession->id;
            $token = $this->tokenManager->createAuthToken(TokenNameEnum::DYNAMIC_lINKS_Token);


            $url = getDynamicLink(
                DynamicLinksEnum::INSTRUCTOR_JOIN_ROOM,
                [
                    'session_id' => $vcrSessionId,
                    'token' => $token,
                    'type' => VCRSessionsTypeEnum::SESSION,
                    'portal_url' => env('VCR_PORTAL_URL', 'https://vcr.ta3lom.com')
                ]
            );

            $actions[] = [

                'endpoint_url' => $url,
                'label' => trans('vcr.Start Session'),
                'method' => 'POST',
                'key' => APIActionsEnums::START_SESSION
            ];
        }
        return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
    }

//    public function includeRecommendation(Exam $exam)
//    {
//        $total = $exam->questions()->count();
//        $correctAnswers = $exam->questions()->where('is_correct_answer', 1)->count();
//        $percent = $total > 0 ? (($correctAnswers / $total) * 100) : 0;
//        if (!$percent == 100) {
//            return $this->item($exam, new ExamReportRecommendationTransformer(), ResourceTypesEnums::EXAM_REPORT_RECOMMENDATION);
//        }
//    }

    public function includeFeedback(Exam $exam)
    {
        return $this->item($exam, new ExamFeedBackTransformer(), ResourceTypesEnums::EXAM_FEEDBACK);
    }

    public function includeQuestions(Exam $exam)
    {
        $questions = $exam->questions;
        $params = [
            'is_answer' => true,
            'disable_actions' => true
        ];

        return $this->collection($questions, new QuestionTransformer($params), ResourceTypesEnums::EXAM_QUESTION);
    }
}
