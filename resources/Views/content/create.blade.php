@extends('Pretzel::admin')
@section('content')

<div class="flex-center position-ref full-height">
    <div class="content">
        <form action="{{action('\Baytek\LaravelContent\Controllers\ContentController@store')}}" method="POST">
            {{ csrf_field() }}

            @include('Pretzel::content.form')

            <input type="submit" value="Save all the things">
        </form>
    </div>
</div>

@endsection