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
        $token = $request->header('Authorization');
        $device_id = $request->header('device_id');
        $device = UserDevice::where(function ($query) use ($device_id, $request){
            $query->where('id', $device_id)
                ->orWhere('device_id', $device_id);
        })->first();
        if (isset($token) && !isset($device)){
            return customResponse(null,__("Device was logged out"), 422, StatusCodesEnum::FAILED);
        }
        return $next($request);
    }
}
