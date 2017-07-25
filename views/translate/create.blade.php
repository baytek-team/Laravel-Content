@extends('contents::admin')
@section('content')

<div class="ui two column stackable grid">
    <div class="ten wide column">
        <h1 class="ui header">
            <i class="browser icon"></i>
            <div class="content">
                {{ ___('Content Translation') }}
                <div class="sub header">{{ ___('Create the content for the application.') }}</div>
            </div>
        </h1>
    </div>
</div>

<div class="ui hidden divider"></div>

<div class="flex-center position-ref full-height">
    <div class="content">
        <form action="{{action('\Baytek\Laravel\Content\Controllers\ContentController@contentStore')}}" method="POST" class="ui form">
            {{ csrf_field() }}

            @include('contents::translate.form')

            <div class="field actions">
	            {{-- <a class="ui button" href="{{ action('\Baytek\Laravel\Content\Controllers\ContentController@contentIndex') }}">{{ ___('Cancel') }}</a> --}}
	            <button type="submit" class="ui right floated primary button">
	            	{{ ___('Save all the things') }}
            	</button>
            </div>
        </form>
    </div>
</div>

@endsection