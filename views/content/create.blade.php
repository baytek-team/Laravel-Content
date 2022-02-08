@extends('Content::admin')

@section('page.head.header')
    <h1 class="ui header">
        <div class="content">
            Create Content
            <div class="sub header">{{ __('Create the content for the application.') }}</div>
        </div>
    </h1>
@endsection

@section('content')
<div class="flex-center position-ref full-height">
    <div class="content">
        <form action="{{ route('content.store')}}" method="POST" class="ui form">
            {{ csrf_field() }}

            @include('Content::content.form')

            <div class="field actions">
	            <a class="ui button" href="{{  route('content.index') }}">{{ __('Cancel') }}</a>
	            <button type="submit" class="ui right floated primary button">
	            	{{ __('Save all the things') }}
            	</button>
            </div>
        </form>
    </div>
</div>

@endsection