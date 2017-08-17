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
	<div class="ui buttons">
		<button class="ui icon button revision @if($content->revision - 1 < 0) disabled @endif" data-revision="{{ $content->revision - 1 }}">
			<i class="backward icon"></i>
		</button>
		<div class="ui simple dropdown button item @if($actualRevisions == 0) disabled @endif"" id="revision" style="border-bottom: none">
			Revision {{ $content->revision }}
			{{-- <i class="dropdown icon"></i> --}}
			<div class="menu">
				@foreach(range($actualRevisions, 0) as $rev)
					<div class="item @if($revision == $rev) @endif" value="{{$rev}}" data-revision="{{$rev}}">Revision {{$rev}}</div class="item">
				@endforeach
			</div>
		</div>

		<button class="ui right icon button revision @if($content->revision + 1 > $actualRevisions) disabled @endif" data-revision="{{ $content->revision + 1 }}">
			<i class="forward icon"></i>
		</button>
	</div>

    <a class="ui primary button" href="{{ route('content.edit', $content) }}">
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

	@if(!empty($content->content))
		<h4 class="ui horizontal divider header">
			<i class="globe icon"></i>
			{{ ___('Content') }}
		</h4>
		<div class="ui basic segment" style='max-height: 400px; overflow: auto'>
			{!! $content->content !!}
		</div>
	@endif

	@if($diff)
		@include('contents::content.partials.differences')
	@endif

	<div class="ui grid">
		<div class="four wide widescreen eight wide computer eight wide tablet sixteen wide mobile column">
			@include('contents::content.partials.attributes')
		</div>
		<div class="four wide widescreen eight wide computer eight wide tablet sixteen wide mobile column">
			@if($content->relations->count())
				@include('contents::content.partials.relations')
			@endif
		</div>
		<div class="four wide widescreen eight wide computer eight wide tablet sixteen wide mobile column">
			@if($content->meta->count())
				@include('contents::content.partials.metadata')
			@endif
		</div>
		<div class="four wide widescreen eight wide computer eight wide tablet sixteen wide mobile column">
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
		$('#revision .item, .revision.button').on('click', function(){
			window.location = '{{ route('content.revision', $content->id) }}/' + $(this)[0].dataset.revision
		})
	</script>
@endsection