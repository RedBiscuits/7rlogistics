<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Supported locales for the API
     */
    private const SUPPORTED_LOCALES = ['en', 'ar'];

    /**
     * Default locale if not specified or invalid
     */
    private const DEFAULT_LOCALE = 'ar';

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->getLocaleFromRequest($request);
        
        app()->setLocale($locale);

        return $next($request);
    }

    /**
     * Get the locale from the request's Accept-Language header.
     */
    private function getLocaleFromRequest(Request $request): string
    {
        $acceptLanguage = $request->header('Accept-Language');

        if (!$acceptLanguage) {
            return self::DEFAULT_LOCALE;
        }

        // Extract the primary language code (e.g., "en-US" -> "en", "ar" -> "ar")
        $locale = strtolower(substr($acceptLanguage, 0, 2));

        if (in_array($locale, self::SUPPORTED_LOCALES)) {
            return $locale;
        }

        return self::DEFAULT_LOCALE;
    }
}
