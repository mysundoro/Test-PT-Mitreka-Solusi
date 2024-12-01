<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Language;

class LoadTranslations
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the user's IP address
        $ipAddress = $request->getClientIp(true);

        // Fetch location data from an IP geolocation API
        $response = Http::get("https://ip-api.com/json/{$ipAddress}");
        $data = $response->json();

        // Check if the request was successful and if we got a country code
        if ($data['status'] === 'success' && isset($data['countryCode'])) {
            $code = $data['countryCode'];
        } else {
            // If ip-api fails, fall back to ipinfo.io
            $fallbackResponse = Http::get("https://ipinfo.io/{$ipAddress}/json");
            $fallbackData = $fallbackResponse->json();
            
            // Check if the fallback request was successful
            if (isset($fallbackData['country'])) {
                $code = $fallbackData['country'];
            } else {
                $code = 'en'; // Default to 'en' if both requests fail
            }
        }

        // Determine the locale based on the country or use the session
        $locale = Session::get('locale');

        if (!$locale) {
            // Fetch the locale code from your LanguageCode model
            $languageCode = \App\Models\LanguageCode::where('code', $code)->first();
            $locale = $languageCode ? $languageCode->code : 'en'; // Fallback to 'en'
            Session::put('locale', $locale);
        }

        App::setLocale($locale);

        // Load translations for the determined locale
        $translations = Language::where('locale', $locale)->pluck('value', 'key')->toArray();
        Session::put('translations', $translations);

        return $next($request);
    }
}
