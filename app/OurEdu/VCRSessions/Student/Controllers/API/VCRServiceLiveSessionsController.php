<?php

    namespace App\OurEdu\VCRSessions\Student\Controllers\API;

    use App\OurEdu\BaseApp\Api\BaseApiController;
    use App\OurEdu\Courses\Models\SubModels\LiveSession;
    use Illuminate\Support\Facades\Auth;
    use App\OurEdu\VCRSessions\Student\Middleware\Api\StudentJoinSessionMiddleware;
    use App\OurEdu\Courses\Repository\LiveSessionRepositoryInterface;
    use App\OurEdu\VCRSessions\ServiceManager\OpenTokServiceManagerInterface;
    use App\OurEdu\VCRSessions\Student\Transformers\JoinedSessionDataTransformer;
    use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;

    class VCRServiceLiveSessionsController extends BaseApiController
    {
        public $user;
        private $liveSessionRepository, $opentok;

        public function __construct(LiveSessionRepositoryInterface $liveSessionRepository, OpenTokServiceManagerInterface $openTok)
        {
            $this->user = Auth::guard('api')->user();
            $this->middleware(StudentJoinSessionMiddleware::class)->only(['studentJoinSession']);

            //register vars
            $this->liveSessionRepository = $liveSessionRepository;
            $this->opentok = $openTok;
        }

        /**
         * subscribed student joining the live session
         * @param LiveSession $sessionId
         */
//        public function studentJoinSession($sessionId)
//        {
//            $session = $this->liveSessionRepository->findOrFail($sessionId);
//
//            //generate new opentok session with default configuration and save session token to database
//            if (!$session->session->session_token) {
//                $this->opentok->setSessionConfig();
//                $newSessionId = $this->opentok->createSession();
//                $session->session->session_token = $newSessionId;
//                $session->save();
//            }
//
//            //join or rejoin student into session and add it to live_session_participants
//            $userParticipant = $session->session->participants()->where('user_id', $this->user->id)->first();
//            if($userParticipant){
//                //update user token
//                $userParticipant->user_token = $this->opentok->generateToken($session->session->session_token,['role'=> 'subscriber']);
//            }else{
//                //create first user token
//                $userParticipant = $session->session->participants()->create([
//                    'user_id' => $this->user->id,
//                    'user_role' => 'subscriber',
//                    'user_token' => $this->opentok->generateToken($session->session->session_token,['role'=> 'subscriber']),
//                ]);
//            }
//            return $this->transformDataModInclude($session->session, '', new JoinedSessionDataTransformer($userParticipant), ResourceTypesEnums::ONLINE_SESSION);
//        }
    }
