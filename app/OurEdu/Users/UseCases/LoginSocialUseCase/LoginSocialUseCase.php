<?php

declare(strict_types=1);

namespace App\OurEdu\Users\UseCases\LoginSocialUseCase;

use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use Laravel\Socialite\Facades\Socialite;
use App\OurEdu\Users\Events\UserModified;
use App\OurEdu\Users\Repository\UserRepositoryInterface;

class LoginSocialUseCase implements LoginSocialUseCaseInterface
{
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function loginOrRegisterByFacebook($data): array
    {
        $socialLoginCase = [];
        $userType = $data['user_type'];
        $allowedWebsites = ['facebook', 'twitter'];


        $providerUser = Socialite::driver($data['providerName'])
            ->fields(['name', 'first_name', 'last_name', 'email', 'gender', 'verified', 'link'])
            ->userFromToken($data['token']);

        // first find by provider
        $user = $this->userRepository->findByProvider($data['providerName'], $providerUser->id);

        // then check if user returned by that provider has the same user type of the current user type request sent
        if($user && $user->type != $userType)
            return array(
                'message' => 'invalid_user_type',
                'detail' => 'auth.cannot login user with sent user type'
            );

//        $user = $this->userRepository->findByProviderAndType($data['providerName'], $providerUser->id, $userType);

        if ($user) {
            // return the registered user
            $socialLoginCase['user'] = $user;
            return $socialLoginCase;
        }

        $user = $this->userRepository->create([
                'first_name' => $providerUser->user['first_name'],
                'last_name' => $providerUser->user['last_name'],
                'language' => config('app.locale'),
                'email' => $providerUser->email,
                'profile_picture' => $providerUser->avatar_original,
                'type' => $data['user_type'],
                'facebook_id' => $providerUser->id
            ]);

        if ($user->type == UserEnums::STUDENT_TYPE) {
            $user->student()->create([
                    'wallet_amount' => '0.00'
                ]);
        }

        if ($user->type == UserEnums::STUDENT_TEACHER_TYPE) {
            $user->studentTeacher()->create([]);
        }

        UserModified::dispatch($data, $user->toArray(), 'User registered using facebook');

        $socialLoginCase['user'] = $user;
        return $socialLoginCase;
    }

    public function twitterAuthentication($data)
    {
        $providerUser = Socialite::driver('twitter')->userFromTokenAndSecret($data->access_token, $data->access_token_secret);

        // first find by provider
        $user = $this->userRepository->findByProvider('twitter', $providerUser->id);

        // then check if user returned by that provider has the same user type of the current user type request sent
        if($user->type != $data->user_type)
            return array(
                'message' => 'invalid_user_type',
                'detail' => 'auth.cannot login user with sent user type'
            );

        if ($user) {
            return $user;
        }

        $user = $this->userRepository->create([
            'first_name' => $providerUser->name,
            'language' => config('app.locale'),
            'email' => $providerUser->email,
            'profile_picture' => $providerUser->avatar,
            'type' => $data->user_type,
            'twitter_id' => $providerUser->id
        ]);

        UserModified::dispatch($data->toArray(), $user->toArray(), 'User registered using Twitter');


        if ($user->type == UserEnums::STUDENT_TYPE) {
            $user->student()->create([
                    'wallet_amount' => '0.00'
                ]);
        }

        if ($user->type == UserEnums::STUDENT_TEACHER_TYPE) {
            $user->studentTeacher()->create([]);
        }

        return $user;
    }
}
