@extends('Pretzel::admin')
@section('content')

<div class="ui two column stackable grid">
    <div class="ten wide column">
        <h1 class="ui header">
            <i class="user icon"></i>
            <div class="content">
                User Management
                <div class="sub header">Manage the users of the claims application.</div>
            </div>
        </h1>
    </div>
    <div class="six wide column right aligned">
        {!! Menu::form(
            ['Delete User' => [
                'action' =>  'Admin\UserController@destroy',
                'method' => 'DELETE',
                'class' => 'ui negative button',
                'prepend' => '<i class="delete icon"></i>',
                'confirm' => 'Are you sure you want to delete user: '.$user->first_name.' '.$user->last_name.'?',
            ]],
            $user)
        !!}
    </div>
</div>
<div class="ui hidden divider"></div>
<div class="ui hidden divider"></div>
<div id="registration" class="et_pb_column ui container">
    <div class="ui hidden divider"></div>
    <form class="ui form" action="{{action('Admin\UserController@update', $user)}}" method="POST">
        {{ csrf_field() }}
        {{ method_field('PUT') }}

        @include('app.admin.user.form')
        <div class="ui hidden divider"></div>
        <div class="ui hidden divider"></div>

        <div class="ui error message"></div>
        <div class="field actions">
            <a class="ui button" href="{{ action('Admin\UserController@index') }}">Cancel</a>
            <button type="submit" class="ui right floated primary button">
                Update User Information
            </button>
        </div>

    </form>
</div>

@endsection