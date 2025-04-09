<?php

namespace App\Http\Middleware;

use App\Enums\StatusCodesEnum;
use App\Modules\Users\Models\UserDevice;
use Closure;
use Illuminate\Http\Request;

class CheckAuthAndDevice
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(!auth('api')->check())
            return customResponse(null,__('auth.Unauthorized'), 401, StatusCodesEnum::UNAUTHORIZED);


        $device_id = $request->header('device-id');
        $device = UserDevice::where(function ($query) use ($device_id, $request){
            $query->where('id', $device_id)
                ->orWhere('device_id', $device_id);
        })->first();
        if (!isset($device)){
            return customResponse(null,__('auth.Device not found'), 401, StatusCodesEnum::FAILED);
        }

        return $next($request);
    }
}
