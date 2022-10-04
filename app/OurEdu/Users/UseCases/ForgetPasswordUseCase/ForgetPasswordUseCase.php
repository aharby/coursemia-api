<?php

namespace App\OurEdu\Users\UseCases\ForgetPasswordUseCase;

use App\OurEdu\BaseApp\Enums\DynamicLinksEnum;
use App\OurEdu\BaseNotification\Jobs\SendActivationCode;
use App\OurEdu\BaseNotification\NotifierFactory\NotifierFactoryInterface;
use App\OurEdu\Users\Auth\Jobs\SendResetPasswordOtpJob;
use App\OurEdu\Users\Models\PasswordReset;
use App\OurEdu\Users\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\OurEdu\Users\Auth\Enum\ResetPasswordEnum;
use App\OurEdu\Users\Repository\UserRepositoryInterface;

class ForgetPasswordUseCase implements ForgetPasswordUseCaseInterface
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
    public function sendPasswordResetMail(
        string $email,
        UserRepositoryInterface $userRepository,
        bool $abilitiesUser = false
    ): array {
        $return = [];
        // find user
        $user = $userRepository->findByEmail($email, $abilitiesUser);

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
                        'data'=> ['url' => $url, 'lang' => $user->language],
                        'subject' => trans('app.OurEdu password reset mail', [], $user->language),
                        'view' => 'passwordResetMail'
                    ]
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

    /**
     * Update user password using reset token
     * @param   $data
     * @param $token
     * @param UserRepositoryInterface $userRepository
     * @return array
     */
    public function updatePasswordUsingResetToken(
        string $token,
        array $data,
        UserRepositoryInterface $userRepository
    ): array {
        $return = [];
        // find token record
        $record = $userRepository->findResetPasswordToken($token);

        if (is_null($record)) {
            $return['message'] = 'Unknown Password Request';
            $return['detail'] = trans('auth.Unknown Password Request');
            $return['code'] = 404;
            return $return;
        }

        $user = null;

        if($record->mobile){
            $user = User::query()->where('mobile', $record->mobile)->first();
        }
        if($record->email){
            $user = User::query()->where('email', $record->email)->first();
        }

        if (!$user) {
            $return['message'] = trans('auth.Unknown Password Request');
            $return['detail'] = trans('auth.Unknown Password Request');
            $return['code'] = 404;
            return $return;
        }

        // change password
        $updatePassword = [
            'password' => $data['password']
        ];
        $userRepository->update($user, $updatePassword);


        // delete user reset tokens
        $userRepository->deleteResetPassword($token);
        $user->update([
            'otp' => null,
        ]);

        $return['message'] = trans('auth.Password Changed Successfully');
        $return['code'] = 200;
        $return['user'] = $user;
        return $return;
    }

    public function sendPasswordResetCode(
        $identifier,
        UserRepositoryInterface $userRepository,
        bool $abilitiesUser = false
    ): array
    {
        $return = [];

        if(!preg_match("/^(05+[^1-2])+([0-9]){7}+$/", $identifier) and !filter_var($identifier, FILTER_VALIDATE_EMAIL))
        {
            $return['message'] = trans('auth.you must enter a valid email or mobile');
            $return['code'] = 422;
            return $return;
        }
        $user = $userRepository->findByEmail($identifier, $abilitiesUser);
        $attribute = 'email';

        $notificationData = [
            'users' => collect([$user]),
            'email' => [
                'user_type' => 'student',
                'view' => 'reset_password_otp',
                'subject' => trans('emails.Forget Password Title'),
            ],
        ];
        if (preg_match("/^(05+[^1-2])+([0-9]){7}+$/", $identifier)) {
            $user = $userRepository->findByPhone($identifier, $abilitiesUser);
            $attribute = 'mobile';
            $notificationData = [
                'users' => collect([$user]),
                'sms' => [
                    'message' => 'app.forget password otp',
                ],
            ];
        }

        if (!$user) {
            $return['message'] = trans('auth.user not found');
            $return['code'] = 422;
            return $return;
        }

        if (Cache::has("user_{$user->id}_reset_mail")) {
            $return['message'] = trans('auth.You may request password reset token once every 3 minutes');
            $return['code'] = 422;
            return $return;
        }

        PasswordReset::query()->where($attribute, $user->$attribute)->delete();

        $token = Str::random(60);
        $resetPassword = [$attribute => $user->$attribute, 'token' => $token, date('Y-m-d')];
        $password = $userRepository->createResetPassword($resetPassword);
        if ($password) {
            SendActivationCode::dispatch($user, $notificationData);
            Cache::remember("user_{$user->id}_reset_mail", now()->addMinutes(2), function () {
                return 1;
            });

            $return['user'] = $user;
            $return['code'] = 200;
            $return['message'] = trans('auth.Password reset token has been sent');
            return $return;
        }

        $return['code'] = 500;
        $return['message'] = trans('app.Oopps Something is broken');
        return $return;
    }

    public function confirmPasswordResetCode($code, UserRepositoryInterface $userRepository)
    {
        $user = $userRepository->findResetPasswordByCode($code)->user;
    }
}
