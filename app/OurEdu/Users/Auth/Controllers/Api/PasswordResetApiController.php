<?php

namespace App\OurEdu\Users\Auth\Controllers\Api;

use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Users\Auth\Enum\ResetPasswordEnum;
use App\OurEdu\Users\Auth\Requests\api\ConfirmResetCodeRequest;
use App\OurEdu\Users\Auth\Requests\api\ResetUserPasswordCodeRequest;
use App\OurEdu\Users\Transformers\ResetPasswordLinkTransformer;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\OurEdu\Users\Events\UserModified;
use App\OurEdu\BaseApp\Api\BaseApiController;
use Illuminate\Support\Facades\Log;
use Swis\JsonApi\Client\Interfaces\ParserInterface;
use App\OurEdu\Users\Repository\UserRepositoryInterface;
use App\OurEdu\Users\Auth\Requests\api\SendResetMailRequest;
use App\OurEdu\Users\Auth\Requests\api\ResetUserPasswordRequest;
use App\OurEdu\Users\UseCases\ForgetPasswordUseCase\ForgetPasswordUseCaseInterface;

class PasswordResetApiController extends BaseApiController
{
    public $parserInterface;
    protected $userRepository;
    protected $forgetPasswordCase;

    public function __construct(
        ParserInterface $parserInterface,
        ForgetPasswordUseCaseInterface $forgetPasswordCase,
        UserRepositoryInterface $userRepository
    ) {
        $this->parserInterface = $parserInterface;
        $this->forgetPasswordCase = $forgetPasswordCase;
        $this->userRepository = $userRepository;
    }

    public function sendPasswordResetMail(SendResetMailRequest $request)
    {
        try {
            //parse data from jsonapi request body
            $data = $request->getContent();
            $data = $this->parserInterface->deserialize($data);
            $data = $data->getData();
            $email = $data->email;
            $abilitiesUser = boolval($request->abilities_user)  ?? false;

            $response = $this->forgetPasswordCase->sendPasswordResetMail($email, $this->userRepository, $abilitiesUser);

            if (isset($response['user'])) {
                return $this->successResponse(['meta' => ['message' => $response['message']]]);
            } else {
                $error = [
                    'status' => 422,
                    'title' => $response['message'],
                    'detail' => $response['message']
                ];
                return formatErrorValidation($error);
            }
        } catch (Exception $e) {
            $errorCatch = [
                'status' => $e->getCode(),
                'title' => $e->getMessage(),
                'detail' => $e->getTrace()
            ];
            return formatErrorValidation($errorCatch, 500);
        }
    }

    public function resetUserPassword(ResetUserPasswordRequest $request, $token)
    {
        try {
            //parse data from jsonapi request body
            $data = $request->getContent();
            $data = $this->parserInterface->deserialize($data);
            $data = $data->getData();
            $dataArray = ['password'=> $data->password];
            $response = $this->forgetPasswordCase->updatePasswordUsingResetToken(
                $token,
                $dataArray,
                $this->userRepository
            );

            if ($response['code'] == 200) {
                UserModified::dispatch([], $response['user']->toArray(), 'User rested password');
                return $this->successResponse(['meta' => ['message' => $response['message']]]);
            } else {
                $error = [
                    'status' => 422,
                    'title' => $response['message'],
                    'detail' => $response['detail']
                ];
                return formatErrorValidation($error);
            }
        } catch (Exception $e) {
            $errorCatch = [
                'status' => $e->getCode(),
                'title' => $e->getMessage(),
                'detail' => $e->getTrace()
            ];
            return formatErrorValidation($errorCatch, 500);
        }
    }

    public function sendResetPasswordCode(ResetUserPasswordCodeRequest $request)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();
        $abilitiesUser = boolval($request->abilities_user)  ?? false;

        $response = $this->forgetPasswordCase->sendPasswordResetCode($data->identifier, $this->userRepository,$abilitiesUser);

        if (isset($response['user'])) {
            return $this->successResponse(['meta' => ['message' => $response['message']]]);
        }

            $error = [
                'status' => $response['code'],
                'title' => $response['message'],
                'detail' => $response['message']
            ];

            return formatErrorValidation($error);
    }

    public function confirmResetCode(ConfirmResetCodeRequest $request)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();

        $user = $this->userRepository->findUserByOtp($data['otp']);
        if($user) {
            $token = $this->userRepository->findResetPasswordTokenByPhoneOrMail($user);
            if ($token) {
                $url = new ResetPasswordEnum($user, $token->token);
                $url = $url->getTypeLink($user->type);
                 $data = [
                     'url' => $url,
                     'token' => $token->token
                 ];
                return $this->transformDataModInclude(
                   [$data],
                    '',
                    new ResetPasswordLinkTransformer(),
                    ResourceTypesEnums::RESET_PASSWORD,
                    ['message' => trans('auth.change password Now')]
                );

            }
        }
        $error = [
            'status' => 500,
            'title' => trans('auth.Unknown Password Request'),
            'detail' => trans('auth.Unknown Password Request')
        ];
        return formatErrorValidation($error);

    }
}
