<?php

namespace App\OurEdu\VCRSessions\General\Transformers;

use App\OurEdu\BaseApp\Api\BaseJsonAgoraHandler;
use App\OurEdu\BaseApp\Enums\DynamicLinksEnum;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Quizzes\Enums\QuizTimesEnum;
use App\OurEdu\Quizzes\Enums\QuizTypesEnum;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSchedules\VCRSessionEnum;
use App\OurEdu\VCRSessions\General\Transformers\QuizzesTransformers\AfterSessionQuizTransformer;
use App\OurEdu\VCRSessions\General\Transformers\QuizzesTransformers\EduSupervisorQuizTransformer;
use App\OurEdu\VCRSessions\General\Transformers\QuizzesTransformers\PreSessionQuizTransformer;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class GetSessionDataTransformer extends TransformerAbstract
{
    protected $params;

    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [
        'preSessionQuiz',
        'afterSessionQuiz',
        'eduSupervisorQuiz',
    ];

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform($vCRSession)
    {
        $user = Auth::guard('api')->user();

        if (in_array($user->type, UserEnums::instructorsUsersTypes())) {
            $userType = $user->type;
            $userUUID = $vCRSession->agora_instructor_uuid;
        }else {
            $userType = $user->type;
            $userUUID = Str::uuid();
            if ($vCRSession->vcr_session_type != VCRSessionEnum::SCHOOL_SESSION) {
                $userUUID = getParticipantFromVCRSession($vCRSession, $user->student)->participant_uuid;
            }
        }

        $roomName = 'session';
        if (isset($vCRSession->request)) {
            $request = $vCRSession->request;
            $roomName = $request->subject->name ?? 'session';
        }
        $paramData = [
            "userName" => $user->name,
            "roomName" => $roomName,
            "role" => (in_array($userType, UserEnums::instructorsUsersTypes())) ? 1 : 2,
            "type" => 2,
            "roomUuid" => $vCRSession->room_uuid,
            "userUuid" => $userUUID,
        ];


        $response = BaseJsonAgoraHandler::makeRequest('/room/entry', 'post', $paramData);
        try {
            $chatRespone = BaseJsonAgoraHandler::makeRequest('room/' . $response['data']['roomId'],
                'GET',
                [],
                $response['data']['userToken']
            );
            $chatState = $chatRespone['data']['room']['muteAllChat'];
            $coVideoUsers = $chatRespone['data']['room']['coVideoUsers'];
            // UnMute Chat
            if ($paramData['role'] == 1 && $chatState == 1) {
                $muteChatData = [
                    "muteAllChat" => 0
                ];

                BaseJsonAgoraHandler::makeRequest('room/' . $response['data']['roomId'],
                    'post',
                    $muteChatData,
                    $response['data']['userToken']
                );
            }
            // Mute chat
            if ($paramData['role'] == 2 && $chatState == 0 && count($coVideoUsers) == 0) {
                $muteChatData = [
                    "muteAllChat" => 1
                ];
                BaseJsonAgoraHandler::makeRequest('room/' . $response['data']['roomId'],
                    'post',
                    $muteChatData,
                    $response['data']['userToken']
                );
            }

            // Disable video and audio for user
            BaseJsonAgoraHandler::makeRequest('room/' . $response['data']['roomId'].'/user/'.$chatRespone['data']['user']['userId'],
                'post',
                [
                    'enableAudio' => 1,
                    'enableVideo' => 0
                ],
                $response['data']['userToken']
            );


        } catch (Exception $exception) {
            Log::channel('slack')->error('User ' . $user->type . ' with id : ' . $user->username . ' Failed to join Vcr session id ' . $vCRSession->id, [
                'Error line' => $exception->getLine(),
                'Error Message' => $exception->getMessage(),
                'agora response' => is_array($response) ? json_encode($response) : $response,
                'agora data sent' => is_array($paramData) ? json_encode($paramData) : $paramData,
            ]);
        }
        $timeToStart = new Carbon(now());
        $timeToEnd = new Carbon($vCRSession->time_to_end);
        $transformerData = [
            'id' => $vCRSession->id,
            'price' => $vCRSession->price.' '.trans('subject_packages.riyal'),
            'status' => $vCRSession->status,
            'student_id' => $vCRSession->student_id,
            'instructor_id' => $vCRSession->instructor_id,
            'subject_id' => (int) $vCRSession->subject_id ?? 0,
            'vcr_request_id' => $vCRSession->vcr_request_id,
            'join_url' => $vCRSession->student_join_url,
            'ended_at' => $vCRSession->ended_at,
            'time_to_start' => $vCRSession->time_to_start,
            'time_to_end' => $vCRSession->time_to_end,
            'time_to_end_in_seconds' => $timeToStart->diffInSeconds($timeToEnd) ?? '',


            'room_name' => $vCRSession->room_uuid,
            'current_user_name' => $user->name,
            'current_user_type' => $user->type,
            'current_user_role' => (in_array($userType, UserEnums::instructorsUsersTypes())) ? 1 : 2,
            'roomId' => $response['data']['roomId'],
            'userToken' => $response['data']['userToken'],

        ];

        $transformerData['generate_exam_link'] = getDynamicLink(DynamicLinksEnum::INSTRUCTOR_GENERATE_EXAM, [
            'session_id'=> $vCRSession->id,
            'portal_url' => env('STUDENT_PORTAL_URL')
        ]);

        if (isset($this->params['share_link'])) {
            $transformerData['share_link'] = $this->params['share_link'];
        }

        return $transformerData;

    }

    public function includePreSessionQuiz(VCRSession $VCRSession)
    {
        $preSessionQuiz = $VCRSession->quizzes()
                            ->where('quiz_type', QuizTypesEnum::QUIZ)
                            ->where('quiz_time', QuizTimesEnum::PRE_SESSION)
                            ->first();

        if ($preSessionQuiz) {
            return $this->item($preSessionQuiz, new PreSessionQuizTransformer() ,ResourceTypesEnums::QUIZ);
        }
    }

    public function includeAfterSessionQuiz(VCRSession $VCRSession)
    {
        $afterSessionQuiz = $VCRSession->quizzes()
            ->where('quiz_type', QuizTypesEnum::QUIZ)
            ->where('quiz_time', QuizTimesEnum::AFTER_SESSION)
            ->first();

        if ($afterSessionQuiz) {
            return $this->item($afterSessionQuiz, new AfterSessionQuizTransformer() ,ResourceTypesEnums::QUIZ);
        }
    }

    public function includeEduSupervisorQuiz(VCRSession $VCRSession)
    {
        $eduSupervisorQuiz = $VCRSession->quizzes()
            ->where('quiz_type', QuizTypesEnum::QUIZ)
            ->where('creator_role', UserEnums::EDUCATIONAL_SUPERVISOR)
            ->first();

        if ($eduSupervisorQuiz) {
            return $this->item($eduSupervisorQuiz, new EduSupervisorQuizTransformer() ,ResourceTypesEnums::QUIZ);
        }
    }
}
