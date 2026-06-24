<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $locale = session('locale');

        if (empty($locale)) {
            $locale = \Config::get('app.locale');
        }

        if (! empty($request->route('lang'))) {
            $locale = $request->route('lang');
            \Session::put('locale', $locale);
        }

        if (lang_exist($locale)) {
            \App::setLocale($locale);
        } else {
            \Session::put('locale', \Config::get('app.locale'));
        }

        return $next($request);

    }
}
