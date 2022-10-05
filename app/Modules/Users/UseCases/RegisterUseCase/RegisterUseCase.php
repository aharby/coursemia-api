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

class RegisterUseCase implements RegisterUseCaseInterface
{
    private $registerStudentUseCase;
    private $registerStudentTeacherUseCase;

    public function __construct(
        RegisterStudentUseCaseInterface $registerStudentUseCase,
        RegisterStudentTeacherUseCaseInterface $registerStudentTeacherUseCase
    )
    {
        $this->registerStudentUseCase = $registerStudentUseCase;
        $this->registerStudentTeacherUseCase = $registerStudentTeacherUseCase;
    }

    public function register(array $request, UserRepositoryInterface $userRepository): User
    {
        $request['language'] = config('app.locale');
        $user =  $userRepository->create($request);
        $user_id = $user->id;
        if ($request['type'] == UserEnums::STUDENT_TYPE) {
            $student = $this->registerStudentUseCase->registerStudent($request, $user_id);
            $this->sendActivationCode($user);
            return $user;
        }

        if ($request['type'] == UserEnums::STUDENT_TEACHER_TYPE) {
            $studentTeacher = $this->registerStudentTeacherUseCase->registerStudentTeacher($request, $user_id);
            $this->sendActivationCode($user);
            return $user;
        }
        $this->sendActivationCode($user);
        return $user;
    }

    private function sendActivationCode(User $user)
    {
        $notificationData = [
            'users' => collect([$user]),
            'sms' => [
                'message' => 'app.Activate Code'
            ]
        ];

        if ($user->email) {
            $notificationData['email'] = [
                'user_type' => $user->type,
                'subject' => trans('app.Activate Account', [], $user->language),
                'view' => 'activateAccountEmail'
            ];

        }

        SendActivationCode::dispatch($user, $notificationData);
    }
}
