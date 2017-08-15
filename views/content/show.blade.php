@extends('contents::admin')

@section('page.head.header')
    <h1 class="ui header">
        {{-- <i class="content icon"></i> --}}
        <div class="content">
            {{ $content->title }}
            {{-- <div class="sub header">{{ ___('Manage the content content type.') }}</div> --}}
        </div>
    </h1>
@endsection

@section('page.head.menu')
    <a class="ui button" href="{{ route('content.edit', $content) }}">
        <i class="pencil icon"></i> {{ ___('Edit content') }}
    </a>
@endsection


@section('content')
<div class="content show">

	<div class="ui grid head">
		<div class="five wide computer sixteen wide mobile column"><div class="ui small ribbon label">Key</div> {{ $content->key }}</div>
		<div class="five wide computer sixteen wide mobile column"><div class="ui small ribbon label">Status</div> {{ $content->statuses()->toFormatted() }}</div>

		<div class="five wide computer sixteen wide mobile column">
			<div class="ui small ribbon label">Revision</div>
			<select class="ui dropdown" name="revision" id="revision">
				@foreach(range($actualRevisions, 0) as $rev)
					<option value="{{$rev}}" @if($revision == $rev) selected @endif>Revision {{$rev}}</option>
				@endforeach
			</select>
		</div>

		<div class="five wide computer sixteen wide mobile column"><div class="ui small ribbon label">Language</div> {{ $content->language }}</div>
		<div class="five wide computer sixteen wide mobile column"><div class="ui small ribbon label">Created</div> {{ $content->created_at->toDayDateTimeString() }}</div>
		<div class="five wide computer sixteen wide mobile column"><div class="ui small ribbon label">Updated</div> {{ $content->updated_at->toDayDateTimeString() }}</div>
	</div>

	<div class="ui hidden divider"></div>
	<div class="ui hidden divider"></div>

	@if(!empty($content->content))
		<h4 class="ui horizontal divider header">
			<i class="globe icon"></i>
			{{ ___('Content') }}
		</h4>

		<div class="ui basic segment" style='max-height: 400px; overflow: auto'>
			{!! $content->content !!}
		</div>

		<div class="ui hidden divider"></div>
		<div class="ui hidden divider"></div>
	@endif

	@if($diff)
		<h4 class="ui horizontal divider header">
			<i class="recycle icon"></i>
			{{ ___('Diff') }}
		</h4>

		<div class="ui basic segment">
			{!! $diff !!}
		</div>
	@endif

	@if($content->meta->count())
		<h4 class="ui horizontal divider header">
			<i class="tags icon"></i>
			{{ ___('Metadata') }}
		</h4>

		<table class="ui very basic table">
			<thead>
				<tr>
					<th>{{ ___('Meta Key') }}</th>
					<th>{{ ___('Meta Value') }}</th>
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
			{{ ___('Relationships') }}
		</h4>

		<table class="ui very basic table">
			<thead>
				<tr>
					<th>{{ ___('Relation Type') }}</th>
					<th>{{ ___('Relation') }}</th>
				</tr>
			</thead>
			<tbody>
				@foreach($content->relations as $relation)
					@if($relation->relation_type_id && $relation->relation_id)
					<tr>
						<td>{{ $content->find($relation->relation_type_id)->title }}</td>
						<td>{{ $content->find($relation->relation_id)->title }}</td>
					</tr>
					@endif
				@endforeach
			</tbody>
		</table>
	@endif

	@if(!empty(config('cms.content.content')))
		<h4 class="ui horizontal divider header">
			<i class="settings icon"></i>
			{{ ___('Settings') }}
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

@section('scripts')
	<script>
		$('#revision').on('change', function(){
			console.log($(this).val())
			window.location = '{{ route('content.revision', $content->id) }}/' + $(this).val()
		})
	</script>
@endsection