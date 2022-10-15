<?php

declare(strict_types=1);

namespace App\Modules\Users\UseCases\RegisterUseCase;

use App\Modules\BaseNotification\Jobs\SendActivationCode;
use App\Modules\Users\Repository\UserRepositoryInterface;
use App\Modules\Users\UseCases\RegisterStudentTeacherUseCase\RegisterStudentTeacherUseCaseInterface;
use App\Modules\Users\UseCases\RegisterStudentUseCase\RegisterStudentUseCaseInterface;
use App\Modules\Users\User;
use App\Modules\Users\UserEnums;
use Illuminate\Support\Facades\Hash;
use Twilio\Rest\Client;

class RegisterUseCase implements RegisterUseCaseInterface
{


    public function register(array $request, UserRepositoryInterface $userRepository): User
    {
        $request['language'] = config('app.locale');
        $user =  $userRepository->create($request);
//        $this->sendActivationCode($user);
        return $user;
    }

    /**
     * Sends sms to user using Twilio's programmable sms client
     * @param String $message Body of sms
     * @param Number $recipients string or array of phone number of recepient
     */
    private function sendActivationCode(User $user)
    {
        $token = getenv("TWILIO_AUTH_TOKEN");
        $twilio_sid = getenv("TWILIO_SID");
        $twilio_verify_sid = getenv("TWILIO_VERIFY_SID");
        $twilio = new Client($twilio_sid, $token);
        $twilio->verify->v2->services($twilio_verify_sid)
            ->verifications
            ->create($user->country_code.$user->phone, "sms");
    }
}
