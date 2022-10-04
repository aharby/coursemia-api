<?php


namespace App\OurEdu\VCRSchedules\UseCases\VCRSessionUseCase;

use App\OurEdu\BaseApp\Api\BaseJsonAgoraHandler;
use App\OurEdu\Payments\Enums\PaymentEnums;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSchedules\Repository\VCRSessionParticipantsRepositoryInterface;
use App\OurEdu\VCRSessions\General\Enums\VCRProvidersEnum;
use App\OurEdu\VCRSessions\General\Enums\VCRSessionsStatusEnum;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\ErrorResponseException;
use App\OurEdu\Users\Repository\UserRepositoryInterface;
use App\OurEdu\VCRSchedules\Repository\VCRRequestRepositoryInterface;
use App\OurEdu\VCRSchedules\Repository\VCRSessionRepositoryInterface;
use Illuminate\Support\Str;

class VCRSessionUseCase implements VCRSessionUseCaseInterface
{
    private $VCRSessionRepository;
    private $VCRRequestRepository;
    private $userRepository;
    private $vcrParticipantRepository;
    protected $user;

    public function __construct(
        VCRSessionRepositoryInterface $VCRSessionRepository,
        VCRRequestRepositoryInterface $VCRRequestRepository,
        UserRepositoryInterface $userRepository,
        VCRSessionParticipantsRepositoryInterface $vcrParticipantRepository
    ) {
        $this->VCRSessionRepository = $VCRSessionRepository;
        $this->VCRRequestRepository = $VCRRequestRepository;
        $this->userRepository = $userRepository;
        $this->vcrParticipantRepository = $vcrParticipantRepository;
        $this->user = Auth::guard('api')->user();
    }

    public function createSession($requestId,string $paymentMethod=PaymentEnums::WALLET)
    {
        $request = $this->VCRRequestRepository->findOrFail($requestId);
        $student = $this->userRepository->findStudentOrFail($request->student_id);

        $returnArr = [];

        if ($student->wallet_amount < $request->price && $paymentMethod == PaymentEnums::WALLET) {
            $returnArr['status'] = 422;
            $returnArr['detail'] = trans('vcr.Student wallet does not have enough amount to request a session');
            $returnArr['title'] = 'wallet_amount';
            return $returnArr;
        }

        $roomUuid = substr(Str::uuid(),0,30);

        $data = [
            'student_id' => $request->student_id,
            'instructor_id' => $request->instructor_id,
            'subject_id' => $request->subject_id,
            'subject_name' => $request->subject->name,
            'vcr_request_id' => $request->id,
            'price' => $request->price,
//            'student_join_url'  =>  $bbbMeeting['attende_url'],
//            'instructor_join_url'  =>  $bbbMeeting['moderator_url'],
            'status' => VCRSessionsStatusEnum::ACCEPTED,
            'room_uuid' => $roomUuid,
            'agora_instructor_uuid' => Str::uuid(),
            'agora_student_uuid' => Str::uuid(),
        ];

        $session = $this->VCRSessionRepository->create($data);


        $participationData = [
            'participant_uuid' => Str::uuid(),
            'vcr_session_id' => $session->id,
            'user_id' => $student->user_id,
        ];
        $this->vcrParticipantRepository->create($participationData);
        $returnArr['status'] = 200;
        $returnArr['detail'] = trans('vcr.Session Created Successfully and should be Started Soon');
        $returnArr['title'] = trans('vcr.Session Created Successfully');
        $returnArr['session_id'] = $session->id;
        return $returnArr;
    }

    public function rateVCRSession($data, $sessionId)
    {
        $vcrSession = $this->VCRSessionRepository->findOrFail($sessionId);

//        if (! $vcrSession->ended_at) {
//            throw new ErrorResponseException(trans('api.You cant rate in progress session'));
//        }

//        if ($vcrSession->student_id != $this->user->student->id) {
//            throw new ErrorResponseException(trans('api.You are not related to this session'));
//        }
//
//        if ($vcrSession->ratings()->where('user_id', $this->user->id)->exists()) {
//            throw new ErrorResponseException(trans('api.You already rated this session'));
//        }

        $vcrSession->ratingUnique([
            'rating'    =>  $data->rating,
            'comment'    =>  $data->comment,
            'instructor_id'    =>  $vcrSession->instructor->id,
        ], $this->user);

        return $vcrSession;
    }

    /**
     * @return string
     */
    public function getSessionMeetingProvider(VCRSession $VCRSession): string
    {
        return $this->getSchoolMeetingType($VCRSession);
    }

    /**
     * @return string
     */
    public function getSystemMeetingType(): string
    {
        $configs = getConfigs();
        $systemMeetingType = $configs['meeting_type'][""] ?? "";

        return in_array($systemMeetingType, VCRProvidersEnum::getList()) ?
            $systemMeetingType :
            VCRProvidersEnum::getDefaultProvider();
    }

    /**
     * @return string
     */
    public function getSchoolMeetingType(VCRSession $VCRSession): string
    {
        $VCRSession->load("classroom.branch.schoolAccount");

        $meetingType = $VCRSession->classroom->branch->meeting_type ?? "";

        return in_array($meetingType, VCRProvidersEnum::getList()) ?
            $meetingType :
            $this->getSystemMeetingType();
    }
}
