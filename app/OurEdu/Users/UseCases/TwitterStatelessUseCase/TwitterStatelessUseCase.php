<?php


namespace App\OurEdu\Users\UseCases\TwitterStatelessUseCase;


use Abraham\TwitterOAuth\TwitterOAuth;
use App\OurEdu\Users\Events\UserModified;
use App\OurEdu\Users\Repository\UserRepositoryInterface;
use App\OurEdu\Users\UserEnums;

class TwitterStatelessUseCase implements TwitterStatelessUseCaseInterface
{
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function callback(array $data)
    {
        $connection = new TwitterOAuth(env('TWITTER_ID'), env('TWITTER_SECRET'), $data['oauth_token'],
            $data['user']);
        $access_token = $connection->oauth("oauth/access_token", ["oauth_verifier" => $data['oauth_verifier']]);

        $connection = new TwitterOAuth(env('TWITTER_ID'), env('TWITTER_SECRET'), $access_token['oauth_token'],
            $access_token['oauth_token_secret']);
        $providerUser = $connection->get("account/verify_credentials");

        $userType = $data['user_type'] ?? env('FALLBACK_SOCIAL_TYPE');

        // first find by provider
        $user = $this->userRepository->findByProvider('twitter', $providerUser->id);

        // then check if user returned by that provider has the same user type of the current user type request sent
        if($user && $user->type != $userType)
            return array(
                'message' => 'invalid_user_type',
                'detail' => 'auth.cannot login user with sent user type',
                'user' => false,
            );


        if ($user) {
            return array(
                'user' => $user,
            );
        }

        $user = $this->userRepository->create([
            'first_name' => $providerUser->name,
            'language' => config('app.locale'),
            'email' => isset($providerUser->email) ?? null,
            'profile_picture' => isset($providerUser->profile_image_url) ?? null,
            'type' => $userType,
            'twitter_id' => $providerUser->id ?? null,
        ]);

        UserModified::dispatch($data, $user->toArray(), 'User Login using Twitter');


        if ($user->type == UserEnums::STUDENT_TYPE) {
            $user->student()->create([
                'wallet_amount' => '0.00'
            ]);
        }

        if ($user->type == UserEnums::STUDENT_TEACHER_TYPE) {
            $user->studentTeacher()->create([]);
        }
            return array(
                'user' => $user,
            );
    }
}
