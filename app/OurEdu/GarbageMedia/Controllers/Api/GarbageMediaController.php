<?php

namespace App\OurEdu\GarbageMedia\Controllers\Api;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Enums\S3Enums;
use App\OurEdu\GarbageMedia\GarbageMedia;
use App\OurEdu\GarbageMedia\Requests\Api\PostImages;
use App\OurEdu\GarbageMedia\Requests\Api\PostMedia;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\GarbageMedia\Transformers\GarbageMediaTransformer;
use Illuminate\Support\Facades\Storage;


class GarbageMediaController extends BaseApiController
{

    public function postImages(PostImages $request){
        $images = $request->images;
        $ids = [];
        foreach ($images as $image){
            $imageName = time().randString(10).'.'.$image->getClientOriginalExtension();
            $imageType = $image->getClientMimeType();
            $originalName = $image->getClientOriginalName();
            $extension = $image->getClientOriginalExtension();
            $pathToUpload = S3Enums::GARBAGE_MEDIA_PATH;
            Storage::putFileAs($pathToUpload, $image,$imageName);
            $grabMedia = GarbageMedia::create([
                'source_filename' => $originalName,
                'filename' => $imageName,
                'mime_type' => $imageType,
                'extension' => $extension,
                'status' => 1
            ]);
            $ids[] = $grabMedia;
        }

        return $this->transformDataMod($ids,  new GarbageMediaTransformer(), ResourceTypesEnums::GARBAGE_MEDIA);

    }

    public function postMedia(PostMedia $request){
        $files = $request->media;
        $ids = [];
        foreach ($files as $file){
            $fileName = time().randString(10).'.'.$file->getClientOriginalExtension();
            $fileType = $file->getClientMimeType();
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $pathToUpload = S3Enums::GARBAGE_MEDIA_PATH;
            Storage::putFileAs($pathToUpload, $file,$fileName);
            $grabMedia = GarbageMedia::create([
                'source_filename' => $originalName,
                'filename' => $fileName,
                'mime_type' => $fileType,
                'extension' => $extension,
                'status' => 1
            ]);

            $ids[] = $grabMedia;
        }
        return $this->transformDataMod($ids,  new GarbageMediaTransformer(), ResourceTypesEnums::GARBAGE_MEDIA);

    }


}
