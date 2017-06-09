@extends('Content::admin')

@section('page.head.header')
    <h1 class="ui header">
        {{-- <i class="content icon"></i> --}}
        <div class="content">
            <small>Editing:</small> {{ $content->title }}
            {{-- <div class="sub header">{{ ___('Manage the content content type.') }}</div> --}}
        </div>
    </h1>
@endsection

@section('content')

<div class="ui container">
    <div class="ui hidden divider"></div>
    <form action="{{ route('content.update', $content) }}" method="POST" class="ui form">
        {{ csrf_field() }}
        {{ method_field('PUT') }}

        @include('Content::content.form')
        <div class="ui hidden divider"></div>
        <div class="ui hidden divider"></div>

        <div class="ui error message"></div>
        <div class="field actions">
            <a class="ui button" href="{{ route('content.index') }}">{{ ___('Cancel') }}</a>
            <button type="submit" class="ui right floated primary button">
                {{ ___('Update Content') }}
            </button>
        </div>
    </form>
</div>

@endsection