<?php

namespace App\OurEdu\GarbageMedia\Transformers;

use App\OurEdu\BaseApp\Enums\S3Enums;
use App\OurEdu\GarbageMedia\GarbageMedia;
use League\Fractal\TransformerAbstract;


class GarbageMediaTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [

    ];

    /**
     * @param GarbageMedia $garbageMedia
     * @return array
     */
    public function transform(GarbageMedia $garbageMedia)
    {
        $transfromedData =  [
            'id' => (int) $garbageMedia->id,
//            'size' => (int) $garbageMedia->size,
            'mime_type' => (string) $garbageMedia->mime_type,
            'url' => (string)getImagePath(S3Enums::GARBAGE_MEDIA_PATH.$garbageMedia->filename),
            'extension' => (string) $garbageMedia->extension,
            'filename' => (string) $garbageMedia->filename,
            'source_filename' => (string) $garbageMedia->source_filename,
        ];
        return $transfromedData;
    }

}
