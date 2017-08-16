@extends('contents::admin')

@section('page.head.header')
    <h1 class="ui header">
        {{-- <i class="content icon"></i> --}}
        <div class="content">
            Editing Contentinium
            <div class="sub header">Escape velocity of 299, 792, 458 m/s</div>
        </div>
    </h1>
@endsection

@section('page.head.menu')
	<select class="ui dropdown item" name="revision" id="revision">
		@foreach(range($actualRevisions, 0) as $rev)
			<option value="{{$rev}}" @if($revision == $rev) selected @endif>Revision {{$rev}}</option>
		@endforeach
	</select>

    <a class="ui button" href="{{ route('content.edit', $content) }}">
        <i class="pencil icon"></i> {{ ___('Edit') }}
    </a>
@endsection


@section('content')
	<h2 class="ui header">
	    {{-- <i class="content icon"></i> --}}
	    <div class="content">
	        {{ $content->title }}
	        {{-- <div class="sub header">{{ ___('Manage the content content type.') }}</div> --}}
	    </div>
	</h2>
	<h4 class="ui horizontal divider header">
		<i class="globe icon"></i>
		{{ ___('Content') }}
	</h4>
	@if(!empty($content->content))
		<div class="ui basic segment" style='max-height: 400px; overflow: auto'>
			{!! $content->content !!}
		</div>
	@endif

	@if($diff)
		@include('contents::content.partials.differences')
	@endif

	<div class="ui grid">
		<div class="four wide column">
			@include('contents::content.partials.attributes')
		</div>
		<div class="four wide column">
			@if($content->meta->count())
				@include('contents::content.partials.metadata')
			@endif
		</div>
		<div class="four wide column">
			{{-- @if($content->relations->count()) --}}
				@include('contents::content.partials.relations')
			{{-- @endif --}}
		</div>
		<div class="four wide column">
			<h4 class="ui horizontal divider header">
				<i class="settings icon"></i>
				{{ ___('Settings') }}
			</h4>
			@if(!empty(config('cms.content.content')))
				<div class="ui segment">
					@php
		    			dump(config('cms.content.content'));
		    		@endphp
				</div>
			@endif
		</div>
	</div>
@endsection

@section('scripts')
	<script>
		$('#revision').on('change', function(){
			console.log($(this).val())
			window.location = '{{ route('content.revision', $content->id) }}/' + $(this).val()
		})
	</script>
@endsection