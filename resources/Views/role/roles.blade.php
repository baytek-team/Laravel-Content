@extends('Pretzel::admin')
@section('content')
<h1 class="ui header">
    <i class="users icon"></i>
    <div class="content">
        Role Management
        <div class="sub header">Manage the roles and permisions for the users of the claims application.</div>
    </div>
</h1>
<div class="ui hidden divider"></div>
<div class="ui hidden divider"></div>

<div class="ui top attached tabular menu">
    <a class="item active" data-tab="role-permissions">Role Permissions</a>
    <a class="item" data-tab="user-roles">User Roles</a>
    <a class="item" data-tab="user-permissions">User Permissions</a>
</div>

<div class="ui bottom attached tab padded segment active" data-tab="role-permissions">
    <form id="role-permissions" action="{{ action('\Baytek\Laravel\Content\Controllers\RoleController@saveRolePermissions') }}" method="POST">
        {{ csrf_field() }}
        <table class="ui selectable celled very basic table">
            <thead>
                <tr>
                    <th></th>
                    @foreach ($roles as $role)
                        <th>{{ $role->name }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($permissions as $permission)
                    <tr>
                        <td class="four wide">{{ $permission->name }}</td>
                        @foreach ($roles as $role)
                            <td>
                                <input type="checkbox" name="{{ $role->name }}[{{ $permission->name }}]" @if($role->hasPermissionTo($permission))checked="checked"@endif />
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="ui hidden divider"></div>
        <button type="submit" class="ui primary icon labeled button"><i class="save icon"></i> Save Role Permissions</button>
    </form>
</div>

<div class="ui bottom attached tab padded segment" data-tab="user-roles">
    <form id="user-roles" action="{{ action('\Baytek\Laravel\Content\Controllers\RoleController@saveUserRoles') }}" method="POST">
        {{ csrf_field() }}
        <table class="ui selectable celled very basic table">
            <thead>
                <tr>
                    <th></th>
                    @foreach ($roles as $role)
                        <th>{{ $role->name }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td class="four wide">{{ $user->first_name . ' ' . $user->last_name }}</td>
                        @foreach ($roles as $role)
                            <td><input type="checkbox" name="{{ $role->name }}[{{ $user->id }}]" @if($user->hasRole($role))checked="checked"@endif /></td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="ui hidden divider"></div>
        <button type="submit" class="ui primary icon labeled button"><i class="save icon"></i> Save User Roles</button>
    </form>
</div>

<div class="ui bottom attached tab padded segment" data-tab="user-permissions">
    <form id="user-permissions" action="{{ action('\Baytek\Laravel\Content\Controllers\RoleController@saveUserPermissions') }}" method="POST">
        {{ csrf_field() }}
        <table class="ui selectable celled very basic table">
            <thead>
                <tr>
                    <th></th>
                    @foreach ($permissions as $permission)
                        <th>{{ $permission->name }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td class="four wide">{{ $user->first_name . ' ' . $user->last_name }}</td>
                        @foreach ($permissions as $permission)
                            <td><input type="checkbox" name="{{ $permission->name }}[{{ $user->id }}]" @if($user->hasPermissionTo($permission))checked="checked"@endif /></td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="ui hidden divider"></div>
        <button type="submit" class="ui primary icon labeled button"><i class="save icon"></i> Save Permissions</button>
    </form>
</div>


@endsection