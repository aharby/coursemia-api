<?php

namespace App\Modules\BaseApp\Middleware;

use Closure;
use Illuminate\Http\Request;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class Locale
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // URL format api/v1/{language}/
        $availableLocales = config("translatable.locales");
        $languageCode = $request->segment(3);
        $languageCode = strtolower($languageCode);

        if (!in_array($languageCode, $availableLocales)) {
            $languageCode = env('DEFAULT_LANGUAGE', 'en');
        }
        LaravelLocalization::setLocale($languageCode);

        $request->route()->forgetParameter('language');
        return $next($request);
    }
}
