<?php

namespace App\Modules\GarbageMedia\Resources\Api;

use App\Modules\BaseApp\Enums\S3Enums;
use Illuminate\Http\Resources\Json\JsonResource;

class ListMedia extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => (int)$this->id,
//            'size' => (int) $garbageMedia->size,
            'mime_type' => (string)$this->mime_type,
            'url' => (string)getImagePath(S3Enums::STORAGE_PATH.S3Enums::GARBAGE_MEDIA_PATH . $this->filename),
            'extension' => (string)$this->extension,
            'filename' => (string)$this->filename,
            'source_filename' => (string)$this->source_filename,
        ];
    }
}
