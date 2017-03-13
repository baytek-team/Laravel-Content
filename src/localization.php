<?php

dump(function_exists('__'));

if (! function_exists('__')) {
    /**
     * Translate the given message.
     *
     * @param  string  $key
     * @param  array  $replace
     * @param  string  $locale
     * @return \Illuminate\Contracts\Translation\Translator|string
     */
    function __($key = null, $replace = [], $locale = null)
    {
        dd($key);
        return app('translator')->getFromJson($key, $replace, $locale);
    }
}