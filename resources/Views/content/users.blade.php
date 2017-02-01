@extends('Content::admin')
@section('content')

<h1 class="ui header">
    <i class="user icon"></i>
    <div class="content">
        User Management
        <div class="sub header">Manage the users of the claims application.</div>
    </div>
</h1>
<div class="ui hidden divider"></div>
<div class="ui hidden divider"></div>

<div class="ui secondary menu">
    <div class="item"><strong>Sort By:</strong></div>

    {!! Sort::links(['id', 'first_name', 'last_name', 'email']) !!}

    <div class="right menu">
        <form class="{{ count($errors) != 0 ? ' error' : '' }}" method="GET">
            <div class="ui left icon right action input">
                <input type="text" placeholder="Search users..." name="search" value="{{ collect(Request::instance()->query)->get('search') }}">
                <i class="users icon"></i>
                <button type="submit" class="ui primary button">Search</button>
            </div>
        </form>
    </div>
</div>
<table class="ui selectable table">
    <thead>
        <tr>
            <th>ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th class="right aligned">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->first_name }}</td>
                <td>{{ $user->last_name }}</td>
                <td>{{ $user->email }}</td>
                <td class="collapsing right aligned">
                    <a class="ui basic primary button" href="{{ action('Admin\UserController@edit', ['user' => $user]) }}">
                        <i class="user icon"></i>
                        Edit User
                    </a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<!-- pagination start -->
<div class="ui hidden divider"></div>
{{ $users->appends(Sort::pagination())->links('app.common.pagination.default') }}
<!-- pagination end -->

@endsection