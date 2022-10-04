<?php

namespace App\OurEdu\Courses\UseCases\CourseSubscribeUseCase;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Enums\DynamicLinksEnum;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Courses\Enums\CourseEnums;
use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Courses\Repository\CourseRepositoryInterface;
use App\OurEdu\Courses\Repository\LiveSessionRepositoryInterface;
use App\OurEdu\Payments\Enums\PaymentEnums;
use App\OurEdu\Payments\Enums\TransactionTypesEnums;
use App\OurEdu\Payments\Repository\PaymentTransactionRepositoryInterface;
use App\OurEdu\Payments\Repository\TransactionRepositoryInterface;
use App\OurEdu\Payments\UseCases\SubmitTransactionUseCase;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\Auth\Enum\TokenNameEnum;
use App\OurEdu\Users\Auth\TokenManager\TokenManagerInterface;
use App\OurEdu\Users\Repository\StudentRepositoryInterface;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\VCRSchedules\Models\LiveSessionParticipant;
use App\OurEdu\VCRSchedules\Repository\VCRSessionParticipantsRepositoryInterface;
use App\OurEdu\VCRSessions\General\Enums\VCRSessionsTypeEnum;
use App\OurEdu\VCRSessions\General\Transformers\GetSessionDataTransformer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizRepositoryInterface;

class CourseSubscribeUseCase implements CourseSubscribeUseCaseInterface
{
    private $student;
    /**
     * @var TokenManagerInterface
     */
    private $tokenManager;

    public function __construct(
        private StudentRepositoryInterface $studentRepo,
        private CourseRepositoryInterface $courseRepo,
        private LiveSessionRepositoryInterface $liveSessionRepo,
        private VCRSessionParticipantsRepositoryInterface $vcrSessionParticipantsRepo,
        private GeneralQuizRepositoryInterface $generalQuizRepo,
        private PaymentTransactionRepositoryInterface $paymentTransactionRepository,
        private SubmitTransactionUseCase $submitTransactionUseCase
    ) {
        $this->tokenManager = app(TokenManagerInterface::class);
    }

    /**
     * @param  int  $courseId
     * @param  int  $studentId
     * @param  bool  $liveSession
     * @return array
     */
    public function subscribeCourse(
        int $courseId,
        int $studentId,
        bool $liveSession = false,
        bool $autoJoin = false,
        string $paymentMethod = PaymentEnums::WALLET
    ): array
    {
        try {
            DB::beginTransaction();
            $returnArr = [];
            $this->student = $this->studentRepo->findOrFail($studentId);
            $homeworks = $this->generalQuizRepo->getCourseHomework($courseId);
            if ($liveSession) {
                $course = $this->liveSessionRepo->findOrFail($courseId);
            } else {
                $course = $this->courseRepo->findOrFail($courseId);
            }

            $transactionData = [
                  'sender_id' => $this->student->user_id,
                  'receiver_id' => $this->student->user_id,
                  'payment_transaction_for' => PaymentEnums::COURSE,
                  'subscribable_id' => $course->id,

            ];

            if ($paymentMethod == PaymentEnums::WALLET) {
                $errors = $this->submitTransactionUseCase->validateSubmitTransaction($transactionData);
                if (count($errors)){
                    return $errors;
                }
                if ($this->student->wallet_amount < $course->subscription_cost) {
                    $returnArr['status'] = 422;
                    $returnArr['detail'] = trans(
                        'course.Your wallet does not have enough amount to subscribe this course'
                    );
                    $returnArr['title'] = 'wallet_amount';
                    return $returnArr;
                }
                $wallet = $this->student->wallet_amount - $course->subscription_cost;
                $this->studentRepo->update($this->student, ['wallet_amount' => $wallet]);
                $transaction = $this->paymentTransactionRepository->create([
                    'amount' => $course->subscription_cost,
                    'sender_id' => $this->student->user_id,
                    'receiver_id' => $this->student->user_id,
                    'payment_transaction_for' => PaymentEnums::COURSE,
                    'payment_transaction_type' => TransactionTypesEnums::WITHDRAWAL,
                    'status'=>PaymentEnums::COMPLETED,
                    'payment_method' => $paymentMethod
                ]);
                $transaction->detail()->create([
                    'subscribable_id' => $course->id,
                    'subscribable_type' => Course::class
                ]);
            }
            $subscribeData = [
                'course_id' => $courseId,
                'instructor_id' => $course->instructor_id,
                'date_of_pruchase' => date('Y-m-d H:i:s')
            ];
            $this->studentRepo->createCourseSubscribe($this->student, $subscribeData);
            foreach ($course->sessions as $session) {
                $participationData = [
                    'participant_uuid' => Str::uuid(),
                    'vcr_session_id' => @$session->VCRSession->id,
                    'user_id' => $this->student->user_id
                ];

                $this->vcrSessionParticipantsRepo->create($participationData);
            }

            if (isset($homeworks)) {
                foreach ($homeworks as $homework) {
                    $this->generalQuizRepo->saveGeneralQuizStudentsSubscribed(
                        $homework,
                        $this->student->user_id
                    );
                }
            }

            DB::commit();
            if ($autoJoin) {
                $returnArr['status'] = 200;
                $returnArr['vcrSessionUrl'] = $this->joinToLiveSession($course->session);
                return $returnArr;
            }
            $returnArr['status'] = 200;
            $returnArr['course'] = $course;
            return $returnArr;
        } catch (\Exception $exception) {
            DB::rollBack();
            $returnArr['status'] = 500;
            $returnArr['detail'] = trans('app.Oopps Something is broken');
            $returnArr['title'] = trans('app.Oopps Something is broken');
            return $returnArr;
        }
    }

    private function joinToLiveSession($courseSession)
    {
        if ($vcrSession = getVCRSessionFromCourseSessionByParticipant($courseSession, $this->student)) {
            $vcrSessionId = $vcrSession->id;
            $token = $this->tokenManager->createUserToken(TokenNameEnum::DYNAMIC_lINKS_Token, $this->student->user);

            return getDynamicLink(DynamicLinksEnum::INSTRUCTOR_JOIN_ROOM,
                ['session_id' => $vcrSessionId, 'token' => $token,
                    'type' => VCRSessionsTypeEnum::LIVE_SESSION,
                    'portal_url' => env('VCR_PORTAL_URL','https://vcr.ta3lom.com')
                ]);
        }
    }
}
