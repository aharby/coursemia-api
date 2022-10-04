<?php

namespace App\OurEdu\AppVersions\Transformers;

use App\OurEdu\AppVersions\AppVersion;
use League\Fractal\TransformerAbstract;

class AppVersionTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];

    protected array $availableIncludes = [

    ];

    /**
     * @param AppVersion $appVersion
     * @return array
     */
    public function transform(AppVersion $appVersion)
    {
        return [
            'id' => $appVersion->id,
            'version' => $appVersion->version,
            'name' => $appVersion->name,
            'version-type' => $appVersion->type
        ];
    }
}
