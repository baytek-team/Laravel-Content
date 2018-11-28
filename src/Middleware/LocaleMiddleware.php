<?php

namespace Baytek\Laravel\Content\Middleware;

use App;
use Carbon\Carbon;
use Closure;
use Session;
use PHPLocale\HttpAcceptLanguage;

class LocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $domain = preg_replace('/.*?:\/\//', '', $request->root());
        $language = App::getLocale();

        // Check the 'Accept-Language' header and set the locale to use that.
        if ($request->server('HTTP_ACCEPT_LANGUAGE')) {
            $language = str_before((new HttpAcceptLanguage)->getLanguage(), '-');
        }
        
        // explode(',', $language)

        // Next check the domain, and override the language
        foreach (config('language.domains') as $localeKey => $localeDomain) {
            if ($localeDomain == $domain) {
                $language = $localeKey;
            }
        }

        // Check the session to see if there's an override from the app
        if (Session::has('locale')) {
            $language = Session::get('locale');
        }

        // Check to see if the request has the locale set in the request
        if (!empty($request->query('lang'))) {
            $language = $request->query('lang');
        }

        if (!is_null($language) && !Session::has('locale')) {

            // Save the locale in the session
            Session::put('locale', $language);

            // Set the app locale
            App::setLocale($language);

            // Set the Carbon locale
            Carbon::setLocale($language); 

            // Set the PHP locale accord to the app locale setting
            setlocale(LC_ALL, config('language.lc_all.'.$language));
        }

        return $next($request);
    }

}

