<?php

namespace App\OurEdu\VCRSessions\General\Transformers;

use App\OurEdu\BaseApp\Api\BaseJsonAgoraHandler;
use App\OurEdu\BaseApp\Enums\DynamicLinksEnum;
use App\OurEdu\GarbageMedia\MediaEnums;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\VCRSchedules\VCRSessionEnum;
use App\OurEdu\VCRSessions\General\Enums\VCRSessionsTypeEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;
use Zttp\Zttp;

class VCRSessionMediaTransformer extends TransformerAbstract
{
    protected $params;

    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [
    ];

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform($sessionMedia)
    {
        $array = [
          'id' => $sessionMedia->id,
          'url' => $sessionMedia->url,
          'mime_type' => (string) $sessionMedia->mime_type,
          'file_name' => $sessionMedia->source_filename,
          'extension' => $sessionMedia->extension,
        ];
        return array_merge($array,MediaEnums::getTypeExtensionsIconDisplay($sessionMedia->extension));
    }
}
