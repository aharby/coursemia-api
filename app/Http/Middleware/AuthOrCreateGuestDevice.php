<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\GuestDevice;

class AuthOrCreateGuestDevice
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {

            if (!Auth::user()->is_active) {
                return response()->json(['message' => 'User is not active'], 403);
            }

            return $next($request);
        }

        // Not authenticated: act as guest
        $deviceId = $request->header('device-id');

        if ($deviceId && !GuestDevice::where('guest_device_id', $deviceId)->exists()) 
            GuestDevice::create([
                'guest_device_id'=> $deviceId
        ]);

        return $next($request);
    }
}
