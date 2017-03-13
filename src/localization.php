<?php

if (! function_exists('___')) {
    /**
     * Translate the given message.
     *
     * @param  string  $key
     * @param  array  $replace
     * @param  string  $locale
     * @return \Illuminate\Contracts\Translation\Translator|string
     */
    function ___($key = null, $replace = [], $locale = null)
    {
        $result = app('translator')->getFromJson($key, $replace, $locale);

        if($key == $result) {
            if(!\DB::table('translations')->where('content', $key)->count()) {
                \DB::table('translations')->insert([
                    'content' => $key,
                ]);
            }
        }

        return $result;
    }
}