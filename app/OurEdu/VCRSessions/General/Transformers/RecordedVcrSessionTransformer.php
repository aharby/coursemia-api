<?php

namespace App\OurEdu\VCRSessions\General\Transformers;

use App\OurEdu\BaseApp\Api\BaseJsonAgoraHandler;
use App\OurEdu\BaseApp\Enums\DynamicLinksEnum;
use App\OurEdu\GarbageMedia\MediaEnums;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\VCRSchedules\VCRSessionEnum;
use App\OurEdu\VCRSessions\General\Enums\VCRSessionsTypeEnum;
use App\OurEdu\VCRSessions\Models\RecordedVcrSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;
use Zttp\Zttp;

class RecordedVcrSessionTransformer extends TransformerAbstract
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

    public function transform(RecordedVcrSession $recordVcrSession)
    {
        $array = [
            'id' => $recordVcrSession->id,
            'url' => $recordVcrSession->url,
            'mime_type' => (string) 'video',
            'file_name' => $recordVcrSession->source_filename,
            'extension' => $recordVcrSession->extension,
        ];
        return array_merge($array,MediaEnums::getTypeExtensionsIconDisplay($recordVcrSession->extension));
    }
}
