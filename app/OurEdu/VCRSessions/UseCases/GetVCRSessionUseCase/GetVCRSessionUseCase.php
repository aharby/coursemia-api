<?php


namespace App\OurEdu\VCRSessions\UseCases\GetVCRSessionUseCase;

use App\OurEdu\BaseNotification\Jobs\LeaveSessionNotifyParentJob;
use App\OurEdu\Users\Auth\TokenManager\TokenManagerInterface;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSchedules\Models\VCRSessionPresence;
use App\OurEdu\VCRSchedules\Repository\VCRSessionRepositoryInterface;
use App\OurEdu\VCRSessions\General\Enums\VCRSessionsStatusEnum;
use App\OurEdu\VCRSessions\UseCases\GetVCRSessionUseCase\VcrType\GetVcrType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use App\OurEdu\Payments\Repository\TransactionRepositoryInterface;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\VCRSchedules\Models\VCRRequest;
use App\OurEdu\VCRSessions\General\Enums\VCRSessionsTypeEnum;

class GetVCRSessionUseCase implements GetVCRSessionUseCaseInterface
{
    protected $user;
    private $VCRSessionRepository;
    /**
     * @var TokenManagerInterface
     */
    private $tokenManager;

    private $transactionRepository;

    public function __construct(
        VCRSessionRepositoryInterface $VCRSessionRepository,
        TransactionRepositoryInterface $transactionRepository
    ) {
        $this->VCRSessionRepository = $VCRSessionRepository;
        $this->user = Auth::guard('api')->user();
        $this->tokenManager = app(TokenManagerInterface::class);
        $this->transactionRepository = $transactionRepository;
    }

    public function getVCRSession(VCRSession $vcrSession, GetVcrType $getVcrType): bool
    {
        $validationErrors = $this->vcrSessionValidations($vcrSession);
        if ($validationErrors) {
            return $validationErrors;
        }

        if ($vcrSession->vcr_session_type == VCRSessionsTypeEnum::REQUESTED_LIVE_SESSION && $this->user->type == UserEnums::STUDENT_TYPE) {
            $this->deductionFromStudentWallet($this->user->student, $vcrSession->vcr_request_id, $vcrSession->price);
        }

        return $getVcrType->execute($vcrSession);
    }

    private function vcrSessionValidations(VCRSession $vcrSession)
    {
        if ($vcrSession->status == VCRSessionsStatusEnum::FINISHED) {
            $return['status'] = 422;
            $return['detail'] = trans('vcr.error getting vcr session, vcr session  has finished');
            $return['title'] = 'error getting vcr session vcr session time has passed';
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
        if (now(Config::get('app.timezone')) > $vcrSession->time_to_end) {
            $return['status'] = 422;
            $return['detail'] = trans('vcr.error getting vcr session, vcr session time has passed');
            $return['title'] = 'error getting vcr session vcr session time has passed';
            return $return;
        }



        if (
            $vcrSession->vcr_session_type == VCRSessionsTypeEnum::REQUESTED_LIVE_SESSION && $this->user->type == UserEnums::STUDENT_TYPE
        ) {
            if ($this->user->student->id != $vcrSession->student_id) {
                $return['status'] = 422;
                $return['detail'] = trans('vcr.not authorized');
                $return['title'] = 'not authorized';
                return $return;
            }

            if (
                $vcrSession->status != VCRSessionsStatusEnum::STARTED &&
                $vcrSession->VCRSessionPresence()->where('user_id',$vcrSession->instructor_id)->whereNull('left_at')->first()
            ) {
                $return['status'] = 422;
                $return['detail'] = trans('vcr.instructor should be in the virtual classroom to enter');
                $return['title'] = 'instructor should be in the virtual classroom to enter';
                return $return;
            }

            if ($this->user->student->wallet_amount < $vcrSession->price) {
                $returnArr['status'] = 422;
                $returnArr['detail'] = trans('vcr.Your wallet does not have enough amount to enter this requested session');
                $returnArr['title'] = 'wallet_amount';
                return $returnArr;
            }
        }
    }


    private function deductionFromStudentWallet(Student $student, int $VCRRequestId, $amount)
    {
        $student->wallet_amount -= $amount;
        $student->save();

        $this->transactionRepository->create([
            'user_id' => $student->user_id,
            'subscribable_id' => $VCRRequestId,
            'subscribable_type' => VCRRequest::class,
            'amount' =>  $amount,
        ]);
    }

    public function leaveSession($sessionId)
    {
        $vcrSession = $this->VCRSessionRepository->findOrFail($sessionId);
        $validationErrors = $this->vcrSessionLeaveValidations($vcrSession);
        if ($validationErrors) {
            return $validationErrors;
        }
        $this->recordVCRUserLeave($vcrSession);
        $useCase['status'] = 200;
        return $useCase;
    }

    private function vcrSessionLeaveValidations(VCRSession $vcrSession)
    {
        $this->vcrSessionValidations($vcrSession);
        $userAttends = VCRSessionPresence::where('vcr_session_id', $vcrSession->id)
            ->where('user_id', $this->user->id)->first();
        if (!$userAttends) {
            $return['status'] = 422;
            $return['detail'] = trans('vcr.user didn\'t join session yet');
            $return['title'] = 'user didn\'t join session';
            return $return;
        }
        if (!is_null($userAttends->left_at)) {
            $return['status'] = 422;
            $return['detail'] = trans('vcr.already left session');
            $return['title'] = 'you have already left session';
            return $return;
        }
    }

    private function recordVCRUserLeave(VCRSession $vcrSession)
    {
        $left_at = Carbon::now();
        VCRSessionPresence::where('vcr_session_id', $vcrSession->id)
            ->where('user_id', $this->user->id)->update(['left_at' => $left_at]);
        LeaveSessionNotifyParentJob::dispatch($this->user, $vcrSession, $left_at->format("Y-m-d H:i"))->onQueue('low')->onConnection('redisOneByOne');
    }
}
