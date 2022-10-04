<?php

namespace App\OurEdu\Users\UseCases\ResetSchoolInstructorPasswordUseCase;

use App\OurEdu\BaseNotification\NotifierFactory\NotifierFactoryInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\OurEdu\Users\Auth\Enum\ResetPasswordEnum;
use App\OurEdu\Users\Repository\UserRepositoryInterface;

class ResetSchoolInstructorPasswordUseCase implements ResetSchoolInstructorPasswordUseCaseInterface
{
    private $notifierFactory;

    public function __construct(NotifierFactoryInterface $notifierFactory)
    {
        $this->notifierFactory = $notifierFactory;
    }

    /**
     * @param $email
     * @param UserRepositoryInterface $userRepository
     * @return array
     */
    public function sendPasswordResetMail(string $email, UserRepositoryInterface $userRepository): array
    {
        $return = [];
        // find user
        $user = $userRepository->findByEmail($email);

        if (! $user) {
            $return['message'] = trans('auth.Unknown Password Request');
            $return['code'] = 422;
            return $return;
        }

        if (Cache::has("user_{$user->id}_reset_mail")) {
            $return['message'] = trans('auth.You may request password reset token once every 3 minutes');
            $return['code'] = 422;
            return $return;
        }else{

            // generate token
            $token = Str::random(60);

            // store token
            $resetPassword = ['email' => $user->email, 'token' => $token, date('Y-m-d')];
            $password = $userRepository->createResetPassword($resetPassword);

            if ($password) {
                $url = new ResetPasswordEnum($user,$token);
                $url = $url->getTypeLink($user->type);

                $notificationData = [
                    'users' => collect([$user]),
                    'mail' => [
                        'user_type' => $user->type,
                        'data'=> ['url' => $url],
                        'subject' => trans('app.OurEdu update instructor password'),
                        'view' => 'update-password'
                    ],
                ];

                $this->notifierFactory->send($notificationData);

                Cache::remember("user_{$user->id}_reset_mail", now()->addMinutes(2), function () {
                    return 1;
                });
                $return['user'] =$user ;
                $return['code'] = 200;
                $return['message'] = trans('auth.Password reset token has been sent to your mail');
                return $return;
            }
        }
        return $return;
    }

}
