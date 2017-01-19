@extends('Pretzel::admin')
@section('content')

<div class="flex-center position-ref full-height">
    <div class="content">
        <form action="{{action('Admin\UserController@store')}}" method="POST">
            {{ csrf_field() }}

            @include('app.admin.user.form')

            <input type="submit" value="Save all the things">
        </form>
    </div>
</div>

@endsection