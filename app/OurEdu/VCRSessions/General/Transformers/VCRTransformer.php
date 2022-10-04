<?php

namespace App\OurEdu\VCRSessions\General\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use League\Fractal\TransformerAbstract;

class VCRTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];
    protected array $availableIncludes = [
    ];

    /**
     * @param VCRSession $vcrSession
     * @return array
     */
    public function transform(VCRSession $vcrSession)
    {
        return [
            'id' => $vcrSession->id,
            'status' => $vcrSession->status
        ];
    }
}
