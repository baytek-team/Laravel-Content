<?php

return [
	'domains' => [
        'en' => env('APP_EN_DOMAIN', 'english.domain'),
        'fr' => env('APP_FR_DOMAIN', 'french.domain'),
    ],

    'lc_all' => [
        'en' => env('APP_LC_ALL_EN', 'en_US'),
        'fr' => env('APP_LC_ALL_FR', 'fr_FR'),
    ],

    'locales' => [
    	'en', 'fr'
    ]
];