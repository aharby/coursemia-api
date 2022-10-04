<?php

namespace App\OurEdu\VCRSessions\General\Controllers\API\V2;

use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseNotification\NotifierFactory\NotifierFactoryInterface;
use App\OurEdu\Courses\Enums\CourseSessionEnums;
use App\OurEdu\Users\Auth\TokenManager\TokenManagerInterface;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSchedules\Repository\VCRSessionParticipantsRepositoryInterface;
use App\OurEdu\VCRSessions\Factories\VCRSessionDataTransformerCreator;
use App\OurEdu\VCRSessions\General\Actions\GetVcrSessionAction;
use App\OurEdu\VCRSessions\General\Enums\VCRSessionsStatusEnum;
use App\OurEdu\VCRSessions\General\Enums\VCRSessionsTypeEnum;
use App\OurEdu\VCRSessions\General\Traits\Zoom;
use App\OurEdu\VCRSessions\General\Transformers\V2\GetSessionDataTransformer;
use App\OurEdu\VCRSessions\UseCases\GetVCRSessionUseCase\GetVCRSessionUseCaseInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use League\Fractal\Serializer\JsonApiSerializer;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class VCRGeneralLiveSessionsController extends BaseApiController
{
    use Zoom;

    public $user;
    public $params;
    private $getVCRSessionUseCase;
    /**
     * @var NotifierFactoryInterface
     */
    private $notifierFactory;
    /**
     * @var VCRSessionParticipantsRepositoryInterface
     */
    private $participantsRepository;
    /**
     * @var TokenManagerInterface
     */
    private $tokenManager;

    public function __construct(
        GetVCRSessionUseCaseInterface $getVCRSessionUseCase,
        TokenManagerInterface $tokenManager
    )
    {
        $this->params = [];
        $this->user = Auth::guard('api')->user();
        $this->getVCRSessionUseCase = $getVCRSessionUseCase;
        $this->tokenManager = $tokenManager;
    }

    /**
     * @param VCRSession $vcrSession
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function getVCRSession(VCRSession $vcrSession)
    {
        $errors = $this->vcrSessionValidations($vcrSession);

        if (!count($errors)) {
            // this block of code handle the presence user session
            $vcrSessionAction = new GetVcrSessionAction($vcrSession);

            if (!$vcrSessionAction->isSuccess()) {
                $errors = array_merge($errors, $vcrSessionAction->getErrors());
            }
            $dataTCreator = new VCRSessionDataTransformerCreator($vcrSession);

            if (!$dataTCreator->isSuccess()) {
                $errors = array_merge($errors, $dataTCreator->getErrors());
            }
        }

        if (count($errors)) {
            return formatErrorValidation($errors);
        }

        return fractal($vcrSession, new GetSessionDataTransformer($dataTCreator->getDataTransformInstance()))
            ->serializeWith(new JsonApiSerializer())
            ->parseIncludes($dataTCreator->getDataTransformInstance()->includes())
            ->withResourceName(ResourceTypesEnums::VCR_SESSION)
            ->toArray();
    }


    public function getZoom()
    {
        // Create account
        $firstName = Str::random(5);
        $lastName = Str::random(5);
        $pathUser = 'users';
        $account = $this->zoomPost(
            $pathUser,
            [
            "action" => "custCreate",
            "user_info" => [
                "email" => Str::random(15) . time() . '@' . Str::random(3) . '.com',
                "type" => 2,
                "first_name" => $firstName,
                "last_name" => $lastName
            ]
            ]
        );
        dd(json_decode($account->body(), true));
        if (!cache()->has('zoomMetings')) {
            // create metting
            $path = 'users/me/meetings';
            $meeting = $this->zoomPost(
                $path,
                [
                'topic' => Str::random(3),
                'type' => 2,
                'start_time' => $this->toZoomTimeFormat(now()->addSecond()),
                'duration' => \request()->get('time') ?? 60,
                'agenda' => Str::random(10),
                'password' => '123',
                'settings' => [
                    'host_video' => false,
                    'participant_video' => false,
                    'waiting_room' => true,
                    'mute_upon_entry' => true,
                    "auto_recording"=> "cloud",
                ]
                ]
            );
            cache()->add('zoomMetings', json_decode($meeting->body(), true)['id'], now()->addMinutes(59));
            cache()->add('zoomMetingUrl', json_decode($meeting->body(), true)['start_url'], now()->addMinutes(59));
        }
        $meetingId = cache()->get('zoomMetings');
        $meetingUrl = cache()->get('zoomMetingUrl');


        // Create account
        $firstName = Str::random(5);
        $lastName = Str::random(5);
        $pathUser = 'users';
        $account = $this->zoomPost(
            $pathUser,
            [
            "action" => "custCreate",
            "user_info" => [
                "email" => Str::random(15) . time() . '@' . Str::random(3) . '.com',
                "type" => 2,
                "first_name" => $firstName,
                "last_name" => $lastName
            ]
            ]
        );
        $userId = json_decode($account->body(), true)['id'];

        // get token
        $pathToken = "/users/{$userId}/token";
        $token = $this->zoomGet($pathToken, []);
        $token = json_decode($token->body(), true)['token'];
        return response()->json(
            [
            'userId' => $userId,
            'token' => $token,
            'meetingId' => $meetingId,
            'meetingUrl' => $meetingUrl,
            'firstName'=>$firstName,
            'lastName'=>$lastName]
        );
    }

    private function vcrSessionValidations(VCRSession $vcrSession): array
    {
        if ($vcrSession->status == VCRSessionsStatusEnum::FINISHED) {
            $return['status'] = 422;
            $return['detail'] = trans('vcr.error getting vcr session, vcr session  has finished');
            $return['title'] = 'error getting vcr session vcr session time has passed';
            return $return;
        }
        if ($vcrSession->status == VCRSessionsStatusEnum::REJECTED) {
            $return['status'] = 422;
            $return['detail'] = trans('vcr.error getting vcr session, vcr session waiting time has passed');
            $return['title'] = 'error getting vcr session vcr session waiting time has passed';
            return $return;
        }
        if ($vcrSession->courseSession  && $vcrSession->courseSession->status == CourseSessionEnums::CANCELED ) {
            $return['status'] = 422;
            $return['detail'] = trans('vcr.error getting vcr session, this vcr session  not active');
            $return['title'] = 'error getting vcr session, this vcr session  not active';
            return $return;
        }

        if (!\auth()->user()->is_active) {
            $this->tokenManager->revokeAuthAllAccessTokens();

            $return['status'] = 422;
            $return['detail'] = trans('auth.This account is suspended');
            $return['title'] = trans('auth.This account is suspended');
            return $return;
        }

        if ($vcrSession->time_to_start > now(Config::get('app.timezone'))) {
            $return['status'] = 422;
            $return['detail'] = trans('vcr.error getting vcr session, vcr session time has not come yet');
            $return['title'] = 'error getting vcr session vcr session time has not come yet';
            return $return;
        }
        if (now(Config::get('app.timezone')) > $vcrSession->time_to_end && $vcrSession->vcr_session_type != VCRSessionsTypeEnum::REQUESTED_LIVE_SESSION) {
            $return['status'] = 422;
            $return['detail'] = trans('vcr.error getting vcr session, vcr session time has passed');
            $return['title'] = 'error getting vcr session vcr session time has passed';
            return $return;
        }

        return [];
    }
}
