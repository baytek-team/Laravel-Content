<?php

namespace Baytek\Laravel\Content\Middleware;

use App;
use Closure;
use Session;

class LocaleMiddleware
{
    // private $languageHeader = 'Accept-Language';
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
        // if($request->hasHeader($this->languageHeader)) {
        //     $language = $request->header($this->languageHeader);
        // }
        // explode(',', $language)

        // Next check the domain, and override the language
        foreach(config('language.domains') as $localeKey => $localeDomain) {
            if($localeDomain == $domain) {
                $language = $localeKey;
            }
        }

        // Check the session to see if there's an override from the app
        if (Session::has('locale')) {
            $language = Session::get('locale');
        }

        if(!is_null($language)) {
            App::setLocale($language);
        }

        return $next($request);
    }

}

