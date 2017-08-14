<?php

Route::resource('content', 'ContentManagementController');
Route::get('content/{id}/revision/{revision?}', 'ContentManagementController@revision')->name('content.revision');
Route::put('translation/{content}/translate', 'ContentController@translate')->name('translation.translate');
Route::get('translation/create', 'ContentController@contentCreate')->name('translation.create');
Route::get('translation/{content}/edit', 'ContentController@contentEdit')->name('translation.edit');

if(!Route::has('admin.index')) {
    Route::group([
        'as' => 'admin.',
    ], function () use ($router) {

    	Route::get('', function(){
            return view('contents::admin');
        })->name('index');

		Route::get('index', function(){
            return view('contents::admin');
        })->name('index');

        Route::get('dashboard', function(){
            return view('contents::admin');
        })->name('dashboard');
    });
}