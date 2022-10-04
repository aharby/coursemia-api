<?php

namespace App\OurEdu\Users\Auth\Controllers\Api;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Api\Requests\BaseApiDataRequest;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Users\Auth\Enum\TokenNameEnum;
use App\OurEdu\Users\Auth\Requests\api\TwitterLoginRequest;
use App\OurEdu\Users\Auth\Requests\api\TwitterStatelessCallbackRequest;
use App\OurEdu\Users\Auth\TokenManager\TokenManagerInterface;
use App\OurEdu\Users\Transformers\UserAuthTransformer;
use App\OurEdu\Users\UseCases\TwitterStatelessUseCase\TwitterStatelessUseCaseInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Swis\JsonApi\Client\Interfaces\ParserInterface;

class TwitterAuthStatelessApiController extends BaseApiController
{
    private $twitterUseCase;
    /**
     * @var TokenManagerInterface
     */
    private $tokenManager;

    public function __construct(
        TwitterStatelessUseCaseInterface $twitterStatelessUseCase,
        ParserInterface $parserInterface,
        TokenManagerInterface $tokenManager
    ) {
        $this->twitterUseCase = $twitterStatelessUseCase;
        $this->parserInterface = $parserInterface;
        $this->tokenManager = $tokenManager;
    }

    public function login()
    {
        $tempId = Str::random(40);
        $connection = new TwitterOAuth(env('TWITTER_ID'), env('TWITTER_SECRET'));
        $token = $connection->oauth(
            'oauth/request_token',
            ['oauth_callback' => env('TWITTER_URL') . '?user=' . $tempId]
        );

        Cache::put($tempId, $token['oauth_token_secret'], 5);
        $url = $connection->url('oauth/authorize', ['oauth_token' => $token['oauth_token']]);
        $data = [
            "data" => [
                "id" => Str::uuid(),
                "type" => "twitterAuth",
                "attributes" => [
                    'url' => $url
                ]
            ]
        ];
        return response()->json($data, 200);
    }

    public function callback(TwitterStatelessCallbackRequest $request)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();
        $data = $data->toArray();

        $user = $this->twitterUseCase->callback($data);

        if(!isset($user['user'])){
            return errorResponse($user['detail']);
        }

        $meta = [
            'token' => $this->tokenManager->createUserToken(TokenNameEnum::API_Token,$user['user']),
            'message' => trans('api.Successfully Logged In'),
        ];

        $include = $data['user_type'] ?? env('FALLBACK_SOCIAL_TYPE');

        //send data to transformer
        return $this->transformDataModInclude(
            $user,
            $include,
            new UserAuthTransformer(),
            ResourceTypesEnums::USER,
            $meta
        );
    }
}
