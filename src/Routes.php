<?php

Route::group(['namespace' => '\Baytek\Laravel\Content\Controllers', 'prefix' => 'admin', 'middleware' => 'web'], function () {
	Route::resource('content', 'ContentController');

	Route::get('roles', 'RoleController@index');
    Route::post('roles/role-permissions', 'RoleController@saveRolePermissions');
    Route::post('roles/user-permissions', 'RoleController@saveUserPermissions');
    Route::post('roles/user-roles', 'RoleController@saveUserRoles');
});