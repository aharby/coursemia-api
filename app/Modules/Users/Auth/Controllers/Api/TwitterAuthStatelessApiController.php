<?php

namespace App\Modules\Users\Auth\Controllers\Api;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Modules\BaseApp\Api\BaseApiController;
use App\Modules\BaseApp\Api\Requests\BaseApiDataRequest;
use App\Modules\BaseApp\Enums\ResourceTypesEnums;
use App\Modules\Users\Auth\Enum\TokenNameEnum;
use App\Modules\Users\Auth\Requests\Api\TwitterLoginRequest;
use App\Modules\Users\Auth\Requests\Api\TwitterStatelessCallbackRequest;
use App\Modules\Users\Auth\TokenManager\TokenManagerInterface;
use App\Modules\Users\Transformers\UserAuthTransformer;
use App\Modules\Users\UseCases\TwitterStatelessUseCase\TwitterStatelessUseCaseInterface;
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
