<?php

namespace App\OurEdu\GarbageMedia\Controllers\Api;

use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\S3Enums;
use App\OurEdu\GarbageMedia\Models\UploadedMedia;
use EdSDK\FlmngrServer\FlmngrServer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends BaseApiController
{
    // Different from GarbageMedia logic, this endpoint for uploading media directly
    //this endpoint for ckeditor only
    public function uploadMedia(Request $request)
    {
        $file = $request->upload;

        $fileSize = $file->getSize();
        if ($fileSize > 5242880) {
            return response()->json(
                [
                    'error' => [
                        "message" => "The image upload failed because the image was too big (max 5MB)."
                    ]
                ]
            );
        }
        $fileName = time() . randString(10) . '.' . $file->getClientOriginalExtension();
        $fileType = $file->getClientMimeType();
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $pathToUpload = S3Enums::UPLOADED_MEDIA_PATH;
        Storage::putFileAs($pathToUpload, $file,$fileName);
        UploadedMedia::create(
            [
                'source_filename' => $originalName,
                'filename' => $fileName,
                'mime_type' => $fileType,
                'extension' => $extension,
                'status' => 1
            ]
        );


        return response()->json(
            [
                'url' => (string)(getImagePath(S3Enums::UPLOADED_MEDIA_PATH.$fileName)),
                'uploaded' => 1,
                'fileName' => $fileName
            ]
        );
    }

    /*
     * @return void
     * */
    public function fileManager(Request $request)
    {
        if ($request->get('action') == null) {
            header('Access-Control-Allow-Origin: *');
        }
        return FlmngrServer::flmngrRequest(
            array(
                'dirFiles' => base_path('/storage/app/public/file-manager/files'),
                'dirCache' => base_path('/storage/app/public/file-manager/files/cache'),
                'dirTmp' => base_path('/storage/app/public/file-manager/files/tmp')
            )
        );
    }
    public function getFileManager(Request $request)
    {
//        if ($request->get('action') == null) {
            header('Access-Control-Allow-Origin: *');
//        }
        return FlmngrServer::flmngrRequest(
            array(
                'dirFiles' => base_path('/storage/app/public/file-manager/files'),
                'dirCache' => base_path('/storage/app/public/file-manager/files/cache'),
                'dirTmp' => base_path('/storage/app/public/file-manager/files/tmp')
            )
        );
    }
}
