@extends('content::admin')

@section('page.head.header')
    <h1 class="ui header">
        <div class="content">
            Create Content
            <div class="sub header">{{ ___('Create the content for the application.') }}</div>
        </div>
    </h1>
@endsection

@section('content')
<div class="flex-center position-ref full-height">
    <div class="content">
        <form action="{{ route('content.store')}}" method="POST" class="ui form">
            {{ csrf_field() }}

            @include('content::content.form')

            <div class="field actions">
	            <a class="ui button" href="{{  route('content.index') }}">{{ ___('Cancel') }}</a>
	            <button type="submit" class="ui right floated primary button">
	            	{{ ___('Save all the things') }}
            	</button>
            </div>
        </form>
    </div>
</div>

@endsection