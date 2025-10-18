<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\GuestDevice;
use App\Modules\Users\Models\UserDevice;
use App\Enums\StatusCodesEnum;

class AuthStudentOrGuest
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth('api')->check()) {

            if (!auth('api')->user()->is_active) {
                return customResponse(null,__('api.user is not active'),
                 401, StatusCodesEnum::FAILED);
            }

            return $next($request);
        }

        $deviceId = $request->header('device-id');

        if(UserDevice::where('device_id', $deviceId)->exists())
            return customResponse(null,__('api.A user with this device exist. please login'),
         401, StatusCodesEnum::FAILED);

        // Not authenticated: act as guest
        
        if ($deviceId && !GuestDevice::where('guest_device_id', $deviceId)->exists()) 
            GuestDevice::create([
                'guest_device_id'=> $deviceId
        ]);

        return $next($request);
    }
}
