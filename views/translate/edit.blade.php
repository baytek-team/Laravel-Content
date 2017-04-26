@extends('Content::admin')
@section('content')

<div class="ui two column stackable grid">
    <div class="ten wide column">
        <h1 class="ui header">
            <i class="browser icon"></i>
            <div class="content">
                {{ ___('Content Translation') }}
                <div class="sub header">{{ ___('Manage the content of the application.') }}</div>
            </div>
        </h1>
    </div>
    <div class="six wide column right aligned">
        {{-- !! Menu::form(
            ['Delete User' => [
                'action' =>  'Admin\UserController@destroy',
                'method' => 'DELETE',
                'class' => 'ui negative button',
                'prepend' => '<i class="delete icon"></i>',
                'confirm' => 'Are you sure you want to delete user: '.$user->first_name.' '.$user->last_name.'?',
            ]],
            $user)
        !! --}}
    </div>
</div>

<div class="ui hidden divider"></div>

<div id="registration" class="ui container">
    <div class="ui hidden divider"></div>
    <form action="{{action('\Baytek\Laravel\Content\Controllers\ContentController@translate', $content)}}" method="POST" class="ui form">
        {{ csrf_field() }}
        {{ method_field('PUT') }}

        @include('Content::translate.form')
        <div class="ui hidden divider"></div>
        <div class="ui hidden divider"></div>

        <div class="ui error message"></div>
        <div class="field actions">
            <a class="ui button" href="{{ action('\Baytek\Laravel\Content\Controllers\ContentController@contentIndex') }}">{{ ___('Cancel') }}</a>
            <button type="submit" class="ui right floated primary button">
                {{ ___('Update Content') }}
            </button>
        </div>
    </form>
</div>

@endsection