<?php

namespace App\Modules\Users\Auth\Controllers\Api;

use App\Http\Requests\Api\LoginRequest;
use Illuminate\Support\Str;
use App\Modules\Users\UserEnums;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\CustomErrorException;
use App\Modules\Users\Events\UserModified;
use App\Modules\BaseApp\Api\BaseApiController;
use App\Modules\Users\Auth\Enum\TokenNameEnum;
use App\Modules\BaseApp\Enums\ResourceTypesEnums;
use App\Modules\Users\Auth\Requests\Api\LogoutRequest;
use App\Modules\Users\Transformers\UserAuthTransformer;
use App\Modules\Users\Auth\Requests\Api\UserActivateOtp;
use App\Modules\Users\Auth\Requests\Api\UserLoginRequest;
use App\Modules\Users\Repository\UserRepositoryInterface;
use App\Modules\Users\Auth\Requests\Api\UserRegisterRequest;
use App\Modules\Users\Auth\Requests\Api\UserTypeDataRequest;
use App\Modules\Users\Auth\Requests\Api\UserBasicDataRequest;
use App\Modules\Users\Auth\Requests\Api\ChangeLanguageRequest;
use App\Modules\Users\Auth\TokenManager\TokenManagerInterface;
use App\Modules\Users\Auth\Requests\Api\UserLoginSocialRequest;
use App\Modules\Users\Auth\Requests\Api\StoreFirebaseTokenRequest;
use App\Modules\Users\UseCases\LoginUseCase\LoginUseCaseInterface;
use App\Modules\Users\Auth\Requests\Api\UserActivateOtpRequest;

class AuthApiController extends BaseApiController
{
    private $loginSocialUseCase;
    protected $firebaseRepository;
    /**
     * @var TokenManagerInterface
     */
    private $tokenManager;

    public function __construct(
        LoginUseCaseInterface $loginUseCase,
        UserRepositoryInterface $userRepository,
//        ParserInterface $parserInterface,
//        RegisterUseCaseInterface $registerUseCase,
//        SendActivationMailUseCaseInterface $sendActivationMailUseCase,
//        ActivateUserUseCaseInterface $activateUserUseCase,
//        LoginSocialUseCase $loginSocialUseCase,
//        FirebaseTokenRepositoryInterface $firebaseRepository,
//        TokenManagerInterface $tokenManager
    )
    {
        $this->loginUseCase = $loginUseCase;
//        $this->registerUseCase = $registerUseCase;
//        $this->sendActivationMailUseCase = $sendActivationMailUseCase;
//        $this->activateUserUseCase = $activateUserUseCase;
        $this->repository = $userRepository;
//        $this->parserInterface = $parserInterface;
//        $this->loginSocialUseCase = $loginSocialUseCase;
//        $this->firebaseRepository = $firebaseRepository;
//        $this->tokenManager = $tokenManager;
    }

    public function postLogin(UserLoginRequest $request)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();

        $requestData = [];
        $requestData['email'] = $data->email;
        $requestData['password'] = $data->password;
        $requestData['device_type'] = $request->device_type;
        $requestData['abilities_user'] = boolval($request->abilities_user)  ?? false;
        $requestData['remember_me'] = null;

        // if login with username
        if (preg_match("/^(05+[^1-2])+([0-9]){7}+$/", $data->email)) {
            $useCase = $this->loginUseCase->login($requestData, $this->repository, 'mobile');
        } elseif (filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
            $useCase = $this->loginUseCase->login($requestData, $this->repository);
        } else {
            $useCase = $this->loginUseCase->loginWithUsername($requestData, $this->repository);
        }

        if (!is_null($useCase['user'])) {
            $authUser = $useCase['user'];

//            if ($authUser->type == UserEnums::STUDENT_TYPE) {
//                $this->tokenManager->revokeUserAccessTokenBy(['name' => TokenNameEnum::API_Token], $authUser);
//            }

            $meta = [
                'token' => $this->tokenManager->createUserToken(TokenNameEnum::API_Token, $authUser),
                'message' => trans('api.Successfully Logged In')
            ];

            $include = '';

            if ($useCase['user']->type == UserEnums::STUDENT_TYPE) {
                $include = $request->include ?? 'student.schoolAccountBranch.schoolAccount';
            }

            if ($useCase['user']->type == UserEnums::PARENT_TYPE) {
                $include = $request->include ?? 'parent';
            }

            if ($useCase['user']->type == UserEnums::INSTRUCTOR_TYPE || $useCase['user']->type == UserEnums::SCHOOL_INSTRUCTOR) {
                $include = $request->include ?? 'instructor';
            }
            if (isset($useCase['shouldChangePassword'])) {
                $param = [
                    'shouldChangePassword' => true
                ];
            }
            //send data to transformer
            return $this->transformDataModInclude($useCase['user'], $include, new UserAuthTransformer(isset($param) ? $param : []), ResourceTypesEnums::USER, $meta);
        } else {
            $errorArray = [
                'status' => $useCase['status'] ?? 422,
                'title' => $useCase['message'],
                'detail' => $useCase['detail'],
            ];
            return formatErrorValidation($errorArray);
        }
    }

    public function login(LoginRequest $request){
        $data = $this->loginUseCase->login($request->all(), $this->repository);
//        return customResponse()
        return $data;
    }

    public function refreshToken()
    {
        // TODO you have to change refresh token to be used passport
        try {
            if (JWTAuth::parseToken()->authenticate()) {
                $newToken = JWTAuth::parseToken()->refresh();

                $meta = [
                    'token' => $newToken,
                ];
                //send data to transformer
                return $this->transformDataModInclude(JWTAuth::user(), '', new UserAuthTransformer(), ResourceTypesEnums::USER, $meta);
            }
        } catch (TokenExpiredException $e) {
            $newToken = JWTAuth::parseToken()->refresh();
            $meta = [
                'token' => $newToken,
            ];
            //send data to transformer
            return $this->transformDataModInclude(JWTAuth::user(), '', new UserAuthTransformer(), ResourceTypesEnums::USER, $meta);
        }
    }

    public function postRegister(UserRegisterRequest $request)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();

        try {
            $requestData = [
                'profile_picture' => isset($data->attach_media) ? moveSingleGarbageMedia($data->attach_media['id'], 'profiles') : null,
                'first_name' => $data->first_name,
                'last_name' => $data->last_name,
                'email' => $data->email,
                'birth_date' => $data->birth_date,
                'country_id' => $data->country_id,
                'password' => $data->password,
                'type' => $data->user_type
            ];

            if ($data->user_type == UserEnums::STUDENT_TYPE) {
                $studentRequestData = [
                    'mobile' => $data->mobile,
                    'educational_system_id' => $data->educational_system_id,
                    'school_id' => $data->school_id,
                    'class_id' => $data->class_id,
                    'academical_year_id' => $data->academical_year_id,
                ];
                $requestData = array_merge($requestData, $studentRequestData);
            }

            if ($data->user_type == UserEnums::PARENT_TYPE) {
                $parentRequestData = [
                    'mobile' => $data->mobile,
                ];
                $requestData = array_merge($requestData, $parentRequestData);
            }

            $user = $this->registerUseCase->register($requestData, $this->repository);


            if (!is_null($user)) {
                UserModified::dispatch($data->toArray(), $user->toArray(), 'User registered');

                return response()->json(
                    [
                        'message' => trans('api.Thanks for registeration')
                    ]
                );
            }
        } catch (\Throwable $e) {
            Log::error($e);
//            throw  $e;
            throw new CustomErrorException($e->getMessage());

        }
    }

    public function getActivate($token)
    {
        try {
            $user = $this->activateUserUseCase->activate($token, $this->repository);

            if (!is_null($user)) {
                $meta = [
                    'token' => $this->tokenManager->createUserToken(TokenNameEnum::API_Token, $user),
                    'message' => trans('api.Account activated')
                ];

                UserModified::dispatch([], $user->toArray(), 'Account activated');

                //send data to transformer
                return $this->transformDataModInclude($user, '', new UserAuthTransformer(), ResourceTypesEnums::USER, $meta);
            } else {
                throw new Exception('invalid token');
            }
        } catch (\Exception $exception) {
            $errorArray = [
                'status' => 422,
                'title' => $exception->getMessage(),
                'detail' => $exception->getMessage(),
            ];
            return formatErrorValidation($errorArray);
        }
    }

    public function getactivateOtp(UserActivateOtpRequest $request)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();
        $code = $data->otp;

            $user = $this->activateUserUseCase->activateWithOtp($code, $this->repository);
            if (!$user) {
               return formatErrorValidation([
                'status' => 422,
                'title' => 'invalid code',
                'detail' => trans('auth.invalid code')
               ]);
            }

        return response()->json([
            'message' => trans('api.Account activated')
        ]);

    }

    public function activateOtp(UserActivateOtp $userActivateOtp)
    {
        $data = $userActivateOtp->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();
        try {
            $user = $this->repository->findUserByOtpTokenAndConfirmToken($data->otp,$data->confirm_token);

            if (!is_null($user)) {
                $meta = [
                    'token' => $this->tokenManager->createUserToken(TokenNameEnum::API_Token, $user),
                ];
                $user->update([
                    'confirm_token' => null,
                    'otp' => null,
                              ]);
                //send data to transformer
                return $this->transformDataModInclude($user, '', new UserAuthTransformer(), ResourceTypesEnums::USER, $meta);
            } else {
                throw new Exception(trans('app.invalid_token'));
            }
        } catch (\Exception $exception) {
            $errorArray = [
                'status' => 422,
                'title' => $exception->getMessage(),
                'detail' => $exception->getMessage(),
            ];
            return formatErrorValidation($errorArray);
        }
    }

    // login and register by social media
    public function loginAndRegisterUsingFacebook(UserLoginSocialRequest $request)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();
        $requestData = [
            'token' => $data->token,
            'secret_id' => $data->secret_id,
            'user_type' => $data->user_type ?? env('FALLBACK_SOCIAL_TYPE'),
            'providerName' => 'facebook'
        ];
        try {
            $useCase = $this->loginSocialUseCase->loginOrRegisterByFacebook($requestData);
            if (!is_null($useCase['user'])) {
                $this->tokenManager->revokeUserAccessTokenBy(['name' => TokenNameEnum::API_Token], $useCase['user']);

                $meta = [
                    'token' => $this->tokenManager->createUserToken(TokenNameEnum::API_Token, $useCase['user']),
                    'message' => trans('api.Successfully Logged In'),
                ];

                $include = '';
                if ($useCase['user']->type == UserEnums::STUDENT_TYPE) {
                    $include = 'student';
                }

                if ($useCase['user']->type == UserEnums::PARENT_TYPE) {
                    $include = 'parent';
                }
                //send data to transformer
                return $this->transformDataModInclude($useCase['user'], $include, new UserAuthTransformer(), ResourceTypesEnums::USER, $meta);
            } else {
                $errorArray = [
                    'status' => 422,
                    'title' => $useCase['message'],
                    'detail' => $useCase['detail'],
                ];
                return formatErrorValidation($errorArray);
            }
        } catch (\Exception $exception) {
            if (Str::contains($exception->getMessage(), '400 Bad Request')) {
                $errorArray = [
                    'status' => 500,
                    'title' => 'invalid_token',
                    'detail' => trans('auth.Maybe the token expired or malformed'),
                ];
                return formatErrorValidation($errorArray, 500);
            } else {
                $errorArray = [
                    'status' => 500,
                    'title' => snake_case($exception->getMessage()),
                    'detail' => $exception->getMessage(),
                ];
                return formatErrorValidation($errorArray, 500);
            }
        }
    }

    public function logout(LogoutRequest $request)
    {
        $user = Auth::guard('api')->user();
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();

        try {
            $this->firebaseRepository->delete($user, $data->toArray());

            $this->tokenManager->revokeAuthAccessToken();

            return response()->json(
                [
                    "meta" => [
                        'message' => trans('api.Successfully Logged Out')
                    ]
                ]
            );
        } catch (\Exception $e) {
            $errorArray = [
                'status' => $e->getCode(),
                'title' => $e->getMessage(),
                'detail' => $e->getTrace()
            ];
            return formatErrorValidation($errorArray, 500);
        }
    }

    public function twitterAuthentication(TwitterLoginRequest $request)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();

        try {
            $user = $this->loginSocialUseCase->twitterAuthentication($data);

            if(!isset($user['user'])){
                return errorResponse($user['detail']);
            }

            $meta = [
                'token' => $this->tokenManager->createUserToken(TokenNameEnum::API_Token, $user),
                'message' => trans('api.Successfully Logged In'),
            ];

            $include = $data->user_type;

            //send data to transformer
            return $this->transformDataModInclude($user, $include, new UserAuthTransformer(), ResourceTypesEnums::USER, $meta);
        } catch (\Exception $exception) {
            if (Str::contains($exception->getMessage(), 'Bad Authentication data')) {
                $errorArray = [
                    'status' => 500,
                    'title' => 'invalid_token',
                    'detail' => trans('auth.Bad authentication data'),
                ];

                return formatErrorValidation($errorArray, 500);
            } elseif (Str::contains($exception->getMessage(), 'Could not authenticate you')) {
                $errorArray = [
                    'status' => 500,
                    'title' => 'invalid_token',
                    'detail' => trans('auth.Bad access token secret'),
                ];

                return formatErrorValidation($errorArray, 500);
            } else {
                $errorArray = [
                    'status' => 500,
                    'title' => snake_case($exception->getMessage()),
                    'detail' => $exception->getMessage(),
                ];

                return formatErrorValidation($errorArray, 500);
            }
        }
    }

    public function changeLanguage(ChangeLanguageRequest $request)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();
        $user = auth()->user();

        try {
            $updateData['language'] = $data->lang_slug;
            $update = $this->repository->update($user, $updateData);
            if ($update) {
                $meta = [
                    'message' => trans('auth.Language changed successfully')
                ];
                return response()->json(['meta' => $meta], 200);
            }
        } catch (\Throwable $e) {
            throw new CustomErrorException($e->getMessage());
        }
    }

    public function storeFCMToken(StoreFirebaseTokenRequest $request)
    {
        $user = Auth::guard('api')->user();
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();

        $token = $this->firebaseRepository->store($user, $data->toArray());

        $meta = [
            'message' => trans('auth.Token stored successfully')
        ];

        return response()->json(['meta' => $meta], 200);
    }

    public function validateBasicData(UserBasicDataRequest $request)
    {

        return response()->json([]);
    }

    public function validateTypeData(UserTypeDataRequest $request)
    {

        return response()->json([]);
    }
}
