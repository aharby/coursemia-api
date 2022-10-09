<?php

namespace App\Modules\GarbageMedia\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Modules\BaseApp\Enums\S3Enums;
use App\Modules\GarbageMedia\GarbageMedia;
use App\Modules\GarbageMedia\Requests\Api\PostMedia;
use App\Modules\GarbageMedia\Resources\Api\ListMedia;
use Illuminate\Support\Facades\Storage;


class GarbageMediaController extends Controller
{

    public function postMedia(PostMedia $request){
        $file = $request->media;
        $ids = [];
//        foreach ($files as $file){
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

//            $ids[] = $grabMedia;
//        }
        return customResponse(new ListMedia($grabMedia),'',true,200);
    }

}
