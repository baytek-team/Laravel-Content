<h4 class="ui horizontal divider header">
	<i class="birthday icon"></i>
	{{ ___('Attributes') }}
</h4>
@php
	$attributes = [
		'key' => $content->key,
		'status' => $content->statuses()->toFormatted() ?: 'None',
		'order' => $content->order ?: 'Date Ascending',
		'language' => $content->language,
		'created' => $content->created_at->toDayDateTimeString(),
		'updated' => $content->updated_at->toDayDateTimeString(),
	];

@endphp
<table class="ui very basic table">
	<tbody>
		@foreach($attributes as $key => $attribute)
			<tr>
				<td>{{ $key }}</td>
				<td>
				@if($key == 'revision')

				@else
					{{ $attribute }}
				@endif
				</td>
			</tr>
		@endforeach
	</tbody>
</table>
{{--
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
</div> --}}