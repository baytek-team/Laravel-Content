<?php

Route::group(['namespace' => '\Baytek\Laravel\Content\Controllers', 'prefix' => 'admin', 'middleware' => 'web'], function () {
	Route::resource('content', 'ContentController');
});