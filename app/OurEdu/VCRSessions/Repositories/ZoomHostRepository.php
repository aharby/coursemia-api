<?php

namespace App\OurEdu\VCRSessions\Repositories;

use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSessions\Enums\ZoomHostStatusEnum;
use App\OurEdu\VCRSessions\Models\ZoomHost;
use Illuminate\Support\Facades\DB;

class ZoomHostRepository implements ZoomHostRepositoryInterface
{
    public function getAvailableHost(VCRSession $VCRSession): ?ZoomHost
    {
        return DB::transaction(function () use ($VCRSession) {
            $available = ZoomHost::query()
                ->where('current_vcr_session_id',$VCRSession->id)
                ->first();
            if(is_null($available)){
                $available = ZoomHost::query()
                    ->where('usage_status', '=', ZoomHostStatusEnum::AVAILABLE)
                    ->orderBy('usages_number')
                    ->lockForUpdate()
                    ->first();
            }
            if ($available) {
                $available->usage_status = ZoomHostStatusEnum::USED;
                $available->usages_number += 1;
                $available->current_vcr_session_id = $VCRSession->id;
                $available->save();
            }
            return $available;
        },3);
    }

    public function freeUsedHost(VCRSession $VCRSession): void
    {
        ZoomHost::query()->where('current_vcr_session_id',$VCRSession->id)->update([
            'usage_status' => ZoomHostStatusEnum::AVAILABLE,
        ]);
    }
}
