<?php

namespace App\Modules\Users\Auth\Controllers\Api;

use App\Modules\BaseApp\Enums\ResourceTypesEnums;
use App\Modules\Users\Auth\Enum\ResetPasswordEnum;
use App\Modules\Users\Auth\Requests\Api\ConfirmResetCodeRequest;
use App\Modules\Users\Auth\Requests\Api\ResetUserPasswordCodeRequest;
use App\Modules\Users\Transformers\ResetPasswordLinkTransformer;
use Exception;
use App\Modules\Users\Events\UserModified;
use App\Modules\BaseApp\Api\BaseApiController;
use App\Modules\Users\Repository\UserRepositoryInterface;
use App\Modules\Users\Auth\Requests\Api\SendResetMailRequest;
use App\Modules\Users\Auth\Requests\Api\ResetUserPasswordRequest;

class PasswordResetApiController extends BaseApiController
{
    public function __construct(
        UserRepositoryInterface $userRepository
    ) {
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
