@extends('Pretzel::admin')
@section('content')

<div class="ui two column stackable grid">
    <div class="ten wide column">
        <h1 class="ui header">
            <i class="browser icon"></i>
            <div class="content">
                Content Management
                <div class="sub header">Create the content for the application.</div>
            </div>
        </h1>
    </div>
</div>

<div class="ui hidden divider"></div>

<div class="flex-center position-ref full-height">
    <div class="content">
        <form action="{{action('\Baytek\Laravel\Content\Controllers\ContentController@store')}}" method="POST" class="ui form">
            {{ csrf_field() }}

            @include('Pretzel::content.form')

            <div class="field actions">
	            <a class="ui button" href="{{ action('\Baytek\Laravel\Content\Controllers\ContentController@index') }}">Cancel</a>
	            <button type="submit" class="ui right floated primary button">
	            	Save all the things
            	</button>
            </div>
        </form>
    </div>
</div>

@endsection