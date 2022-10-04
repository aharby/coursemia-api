<?php

namespace App\OurEdu\Users\UseCases\CreateZoomUserUserCase;

use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\VCRSessions\General\Models\UserZoom;
use App\OurEdu\VCRSessions\General\Traits\Zoom;
use CURLFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use function formatErrorValidation;
use function response;
use function trans;

class CreateZoomUserUseCase implements CreateZoomUserUseCaseInterface
{
    use Zoom;

    public function createUser(User $user): array
    {
        if (in_array($user->type, UserEnums::allowedUserUsingZoom()) and !$user->zoom) {
            $firstName = $user->first_name ?? Str::random(5);
            $lastName = $user->last_name ?? Str::random(5);
            $pathUser = 'users';
            $account = $this->zoomPost(
                $pathUser,
                [
                   'action' => 'custCreate',
                   'user_info' => [
                       'email' => Str::random(8) . time() . '_manasat@ikcedu.net',
                       'type' => 1,
                       'first_name' => $firstName,
                       'last_name' => $lastName
                   ]
                ]
            );
            $body = json_decode($account->body(), true);


            if ($account->status() == 201) {
                UserZoom::create(
                    [
                       'user_id' => $user->id,
                       'zoom_id' => $body['id']
                    ]
                );

                $this->changeProfilePicture($body['id'], $user->profile_picture);

                return [
                    'status' => '201',
                    'error' => false,
                    'message' => trans('users.created_successfully')
                ];
            }
            return [
                'error' => true,
                'title' => trans('app.Oopps Something is broken'),
                'detail' => $body['message'] ?? trans('app.Oopps Something is broken'),
                'status' => $account->status()
            ];
        }
        return [
            'error' => false,
            'status' => '200',
        ];
    }

    public function changeProfilePicture(string $zoomUserId, string $picturePath = null)
    {
        $picturePath = imageProfileApi($picturePath ?? "");

        $fields = [
            'pic_file' => new CURLFile($picturePath, 'image/jpg', 'test.jpg'),
        ];

        $curl = curl_init();
        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => $this->retrieveZoomUrl() . "users/$zoomUserId/picture",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $fields ,
                CURLOPT_HTTPHEADER => array(
                    "authorization: Bearer {$this->generateZoomToken()}",
                    "content-type: multipart/form-data"
                ),
            )
        );

        curl_exec($curl);
        curl_close($curl);
    }

    public function changeHostProfileImage($zoomUserID)
    {
        $picturePath = public_path('img/t3lom-png.7b09fc3.png');

        $fields = [
            'pic_file' => new CURLFile($picturePath, 'image/jpg', 'test.jpg'),
        ];

        $curl = curl_init();
        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => $this->retrieveZoomUrl() . "users/$zoomUserID/picture",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $fields ,
                CURLOPT_HTTPHEADER => array(
                    "authorization: Bearer {$this->generateZoomToken()}",
                    "content-type: multipart/form-data"
                ),
            )
        );

        curl_exec($curl);
        curl_close($curl);
    }
}
