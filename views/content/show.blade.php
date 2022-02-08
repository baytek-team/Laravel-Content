@extends('Content::admin')

@section('page.head.header')
    <h1 class="ui header">
        {{-- <i class="content icon"></i> --}}
        <div class="content">
            {{ $content->title }}
            {{-- <div class="sub header">{{ __('Manage the content content type.') }}</div> --}}
        </div>
    </h1>
@endsection

@section('page.head.menu')
    <a class="ui button" href="{{ route('content.edit', $content) }}">
        <i class="pencil icon"></i> {{ __('Edit content') }}
    </a>
@endsection

@section('content')
<div class="webpage" style="background: {{ config('cms.content.webpage.background') }}">

	<table class="ui celled table">
		<tbody>
			<tr>
				<td><a style="min-width: 100px;text-align: right" class="ui ribbon label">Key</a> {{ $content->key }}</td>
				<td><a style="min-width: 100px;text-align: right" class="ui ribbon label">Status</a> {{ $content->statuses()->toFormatted() }}</td>
			</tr>
			<tr>
				<td><a style="min-width: 100px;text-align: right" class="ui ribbon label">Title</a> {{ $content->title }}</td>
				<td><a style="min-width: 100px;text-align: right" class="ui ribbon label">Created</a> {{ $content->created_at->toDayDateTimeString() }}</td>
			</tr>
			<tr>
				<td><a style="min-width: 100px;text-align: right" class="ui ribbon label">Language</a> {{ $content->language }}</td>
				<td><a style="min-width: 100px;text-align: right" class="ui ribbon label">Updated</a> {{ $content->updated_at->toDayDateTimeString() }}</td>
			</tr>
		</tbody>
	</table>

	<div class="ui hidden divider"></div>
	<div class="ui hidden divider"></div>

	@if(!empty($content->content))
		<h4 class="ui horizontal divider header">
			<i class="globe icon"></i>
			{{ __('Content') }}
		</h4>

		<div class="ui basic segment" style='max-height: 400px; overflow: auto'>
			{!! $content->content !!}
		</div>

		<div class="ui hidden divider"></div>
		<div class="ui hidden divider"></div>

	@endif

	@if($content->meta->count())
		<h4 class="ui horizontal divider header">
			<i class="tags icon"></i>
			{{ __('Metadata') }}
		</h4>

		<table class="ui celled table">
			<thead>
				<tr>
					<th>{{ __('Meta Key') }}</th>
					<th>{{ __('Meta Value') }}</th>
				</tr>
			</thead>
			<tbody>
				@foreach($content->meta as $meta)
					<tr>
						<td>{{ $meta->key }}</td>
						<td>{{ $content->metadata($meta->key) }}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	@endif

	@if($content->relations->count())
		<h4 class="ui horizontal divider header">
			<i class="users icon"></i>
			{{ __('Relationships') }}
		</h4>

		<table class="ui celled table">
			<thead>
				<tr>
					<th>{{ __('Relation Type') }}</th>
					<th>{{ __('Relation') }}</th>
				</tr>
			</thead>
			<tbody>
				@foreach($content->relations as $relation)
					<tr>
						<td>{{ $content->find($relation->relation_type_id)->title }}</td>
						<td>{{ $content->find($relation->relation_id)->title }}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	@endif

	@if(!empty(config('cms.content.content')))
		<h4 class="ui horizontal divider header">
			<i class="settings icon"></i>
			{{ __('Settings') }}
		</h4>
		<div class="ui segment">
			@php
    			dump(config('cms.content.content'));
    		@endphp
		</div>

		<div class="ui hidden divider"></div>
		<div class="ui hidden divider"></div>

	@endif

	@if($content->revision)
		<h4 class="ui horizontal divider header">
			<i class="settings icon"></i>
			{{ __('Revisions') }}
		</h4>
		<div class="ui segment">
			@php
    			dump(config('cms.content.content'));
    		@endphp
		</div>

		<div class="ui hidden divider"></div>
		<div class="ui hidden divider"></div>

	@endif

</div>

@endsection