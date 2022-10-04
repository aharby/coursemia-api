<?php
declare(strict_types=1);

namespace App\OurEdu\Users\UseCases\UpdateProfileUseCase;

use App\OurEdu\Users\Repository\UserRepositoryInterface;
use App\OurEdu\Users\UseCases\CreateZoomUserUserCase\CreateZoomUserUseCaseInterface;
use App\OurEdu\Users\UseCases\SendActivationMailUseCase\SendActivationMailUseCaseInterface;
use App\OurEdu\Users\UseCases\UpdateContentAuthorUserCase\UpdateContentAuthorUseCaseInterface;
use App\OurEdu\Users\UseCases\UpdateInstructorUseCase\UpdateInstructorUseCaseInterface;
use App\OurEdu\Users\UserEnums;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UpdateProfileUseCase implements UpdateProfileUseCaseInterface
{
    private $sendActivationEmail;
    private $contentAuthorUseCase;
    private $instructorUseCase;
    private CreateZoomUserUseCaseInterface $createZoomUserUseCase;

    public function __construct(
        SendActivationMailUseCaseInterface $sendActivationMailUseCase,
        UpdateContentAuthorUseCaseInterface $updateContentAuthorUseCase,
        UpdateInstructorUseCaseInterface $updateInstructorUseCase,
        CreateZoomUserUseCaseInterface $createZoomUserUseCase,
    )
    {
        $this->sendActivationEmail = $sendActivationMailUseCase;
        $this->contentAuthorUseCase = $updateContentAuthorUseCase;
        $this->instructorUseCase = $updateInstructorUseCase;
        $this->createZoomUserUseCase = $createZoomUserUseCase;
    }

    /**
     * @param array $data
     * @param UserRepositoryInterface $userRepository
     * @return array
     */
    public function updateProfile(array $data, UserRepositoryInterface $userRepository): array
    {
        $return = [];
        $auth = checkLoginGuard();
        $user = $userRepository->findOrFail($auth->id());
        if(!$user->added_by_social) {
            if (array_key_exists('password', $data) && isset($data['old_password'])) {
                if (array_key_exists('old_password', $data)) {
                    if (!Hash::check(trim($data['old_password']), trim($user->password))) {
                        $return['code'] = 422;
                        $return['message'] = trans('api.Wrong old Password');
                        $return['title'] = 'Wrong old Password';
                        return $return;
                    }
                } else {
                    $return['code'] = 422;
                    $return['message'] = trans('api.Old Password required');
                    $return['title'] = 'Wrong old Password';
                    return $return;
                }
            }

            if (!(isset($data['email']) && filter_var($data['email'], FILTER_VALIDATE_EMAIL))) {
                $data['email'] = $user->email;
            }

            if (!(isset($data['mobile']) && preg_match("/^(05+[^1-2])+([0-9]){7}+$/", $data['mobile']))) {
                $data['mobile'] = $user->mobile;
            }

            if ($user->email != $data['email'] || $user->mobile != $data['mobile']) {
                if (array_key_exists('old_password', $data) && strlen($data['old_password']) > 0) {
                    if (!Hash::check(trim($data['old_password']), trim($user->password))) {
                        $return['code'] = 422;
                        $return['title'] = 'Wrong old Password';
                        $return['message'] = trans('api.Wrong old Password');
                        return $return;
                    }
                } else {
                    $return['code'] = 422;
                    $return['title'] = 'Old Password required';
                    $return['message'] = trans('validation.required',['attribute'=>trans('validation.password')]);
                    return $return;
                }
            }
        }

        if ($userRepository->update($user, $data)) {
            if (auth('api')->check() && isset($data['profile_picture'])) {
                $data['profile_picture'] = moveSingleGarbageMedia($data['profile_picture'], 'profiles');

                if ($user->type == UserEnums::SCHOOL_INSTRUCTOR) {
                    $this->drawBorder(storage_path('app/public/uploads/' . "small" . '/' . $data['profile_picture']));
                    $this->drawBorder(storage_path('app/public/uploads/' . "large" . '/' . $data['profile_picture']));
                }

            } else {
                if (isset($data['profile_picture'])) {
                    $data['profile_picture'] = 'users/profiles/' . $user->fresh()->profile_picture;
                }
            }
            $userRepository->update($user, $data);
            if ($user->type == UserEnums::STUDENT_TYPE) {
                $studentData = [];

                if (isset($data['educational_system']) && !is_null($data['educational_system'])) {
                    $studentData['educational_system_id'] = $data['educational_system'];
                }
                if (isset($data['school']) && !is_null($data['school'])) {
                    $studentData['school_id'] = $data['school'];
                }
                if (isset($data['class']) && !is_null($data['class'])) {
                    $studentData['class_id'] = $data['class'];
                }
                if (isset($data['academical_year']) && !is_null($data['academical_year'])) {
                    $studentData['academical_year_id'] = $data['academical_year'];
                }
                if (isset($data['birth_date']) && !is_null($data['birth_date'])) {
                    if (Str::length($data['birth_date']) == 0) {
                        $studentData['birth_date'] = null;
                    } else {
                        $studentData['birth_date'] = $data['birth_date'];
                    }
                }
                $userRepository->updateStudent($user, $studentData);
                $zoomAccount = $user->zoom;
                if (!$zoomAccount) {
                    $this->createZoomUserUseCase->createUser($user);
                    $zoomAccount = $user->zoom;
                }

                if ($user->isDirty("profile_picture")) {
                    $this->createZoomUserUseCase->changeProfilePicture($zoomAccount->zoom_id, $user->profile_picture);
                }
            } else if ($user->type == UserEnums::CONTENT_AUTHOR_TYPE) {
                $contentAuthorData = [];
                if (isset($data['hire_date'])) {
                    $contentAuthorData['hire_date'] = $data['hire_date'];
                }
                $this->contentAuthorUseCase->UpdateContentUseCase($user->id, $contentAuthorData);
            } else if ($user->type == UserEnums::INSTRUCTOR_TYPE) {
                $instructorData = [];
                if (isset($data['hire_date'])) {
                    $instructorData['hire_date'] = $data['hire_date'];
                }
                if (isset($data['school_id'])) {
                    $instructorData['school_id'] = $data['school_id'];
                }
                $this->instructorUseCase->UpdateInstructorCase($user->id, $instructorData);
            }

            $return['code'] = 200;
            $return['message'] = trans('app.Update successfully');
            $return['user'] = $userRepository->findOrFail($auth->id());
            return $return;
        }
        $return['code'] = 500;
        $return['message'] = trans('app.Oopps Something is broken');
        $return['title'] = 'Oopps Something is broken';
        return $return;
    }


    public function updatePassword(array $data, UserRepositoryInterface $userRepository): array
    {
        $return = [];

        $auth = checkLoginGuard();
        $user = $userRepository->findOrFail($auth->id());
        if (is_null($data['old_password'])) {
            if ($user->confirm_token) {
                if ($userRepository->update($user, $data)) {
                    $userRepository->update($user, ['confirm_token' => null]);
                    $return['code'] = 200;
                    $return['message'] = trans('app.Update successfully');
                    $return['user'] = $user;
                    return $return;
                }
            } else {
                $return['code'] = 422;
                $return['message'] = trans('api.Old Password required');
                $return['title'] = 'Old Password required';
                return $return;
            }
        }
        if (array_key_exists('password', $data) && isset($data['old_password'])) {
            if (array_key_exists('old_password', $data)) {
                if (!Hash::check(trim($data['old_password']), trim($user->password))) {
                    $return['code'] = 422;
                    $return['message'] = trans('api.Wrong old Password');
                    $return['title'] = 'Wrong old Password';
                    return $return;
                }
            } else {
                $return['code'] = 422;
                $return['message'] = trans('api.Old Password required');
                $return['title'] = 'Old Password required';
                return $return;
            }
        }
        if ($userRepository->update($user, $data)) {
            $return['code'] = 200;
            $return['message'] = trans('app.Update successfully');
            $return['user'] = $userRepository->findOrFail($auth->id());
            return $return;
        }
        $return['code'] = 500;
        $return['message'] = trans('app.Oopps Something is broken');
        $return['title'] = 'Oopps Something is broken';
        return $return;
    }

    public function updateLanguage(array $data, UserRepositoryInterface $userRepository): array
    {
        $return = [];

        $auth = checkLoginGuard();
        $user = $userRepository->findOrFail($auth->id());


        if ($userRepository->update($user, $data)) {
            $return['code'] = 200;
            $return['message'] = trans('app.Update successfully');
            $return['user'] = $userRepository->findOrFail($auth->id());
            return $return;
        }
        $return['code'] = 500;
        $return['message'] = trans('app.Oopps Something is broken');
        $return['title'] = 'Oopps Something is broken';
        return $return;
    }

    public function drawBorder($path)
    {
        if (file_exists($path)) {

            $path_parts = pathinfo($path);

            $extension = isset($path_parts["extension"]) ? $path_parts["extension"] : "";

            if ($extension == "jpg" || $extension == "jpeg") {

                $image = imagecreatefromjpeg($path);

                $color_gold = imagecolorallocate($image, 255, 223, 0);
                $this->draw($image, $color_gold, 10);

                imagejpeg($image, $path);
            }

            if ($extension == "png") {

                $image = imagecreatefrompng($path);

                $color_gold = imagecolorallocate($image, 255, 223, 0);
                $this->draw($image, $color_gold, 10);

                imagepng($image, $path);
            }
        }
    }

    public function draw($image, $color, $thickness)
    {
        $x1 = 0;
        $y1 = 0;
        $x2 = ImageSX($image) - 1;
        $y2 = ImageSY($image) - 1;

        for ($i = 0; $i < $thickness; $i++) {
            imageRectangle($image, $x1++, $y1++, $x2--, $y2--, $color);
        }
    }

}
