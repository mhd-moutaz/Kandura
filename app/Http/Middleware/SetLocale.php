<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * Sets the application locale based on session preference.
     * Falls back to app default if no preference is stored.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $availableLocales = config('app.my_langs', ['en', 'ar']);
        $locale = session('locale', config('app.locale'));

        if (in_array($locale, $availableLocales)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
