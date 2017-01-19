<?php

namespace Baytek\LaravelContent\Controllers;

use App\User;

use Baytek\LaravelContent\Models\Content;
use Baytek\LaravelContent\Models\ContentMeta;
use Baytek\LaravelContent\Requests\RoleRequest;

use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
	public function index()
	{
		return view('Pretzel::role.roles', [
			'roles' => Role::all(),
			'users' => User::all(),
			'permissions' => Permission::all(),
		]);
	}

	public function saveRolePermissions(RoleRequest $post)
	{
		Role::all()->each(function($role){
			$role->permissions()->detach();
		});

		foreach($post->request as $role => $permissions) {

			if(is_array($permissions)) {
				$roleModel = Role::findByName($role);

				$permissions = collect(array_keys($permissions))
					->flatten()
					->map(function ($permission) {
						return app(Permission::class)->findByName($permission);
					})
					->all();

				$roleModel->permissions()->saveMany($permissions);
			}
		}

		return redirect()->action('\Baytek\LaravelContent\Controllers\RoleController@index');
	}

	public function saveUserRoles(RoleRequest $post)
	{
		User::all()->each(function($user){
			$user->roles()->detach();
		});

		foreach($post->request as $role => $users) {

			if(is_array($users)) {
				$roleModel = Role::findByName($role);

				collect(array_keys($users))->each(function ($user) use ($roleModel) {
					User::find($user)->assignRole($roleModel);
				});
			}
		}

		return redirect()->action('\Baytek\LaravelContent\Controllers\RoleController@index');
	}

	public function saveUserPermissions(RoleRequest $post)
	{
		User::all()->each(function($user){
			$user->permissions()->detach();
		});

		foreach($post->request as $permission => $users) {

			if(is_array($users)) {
				$permissionModel = Permission::findByName(str_replace('_', ' ', $permission));

				collect(array_keys($users))->each(function ($user) use ($permissionModel) {
					User::find($user)->givePermissionTo($permissionModel);
				});
			}
		}

		return redirect()->action('\Baytek\LaravelContent\Controllers\RoleController@index');
	}

}
