<p>{{ $content->title }}</p>

<div class="field{{ $errors->has('title') ? ' error' : '' }}">
	<label for="title">Title</label>
	<input type="text" id="title" name="title" placeholder="Title" value="{{ old('title', $content->title) }}">
</div>

<p>{{ $content->content }}</p>
<div class="field{{ $errors->has('content') ? ' error' : '' }}">
	<label for="content">Content</label>
	<textarea id="content" name="content" placeholder="Content">{{ old('content', $content->content) }}</textarea>
</div>

<h4 class="ui horizontal divider header">
	<i class="tags icon"></i>
	Metadata
</h4>

<input type="hidden" name="meta_ids" value="{{ json_encode($content->meta->pluck('id')) }}">

@foreach($content->meta as $meta)
	@if($meta == $content->meta->first())
		<div class="two fields">
			<div class="field">
				<label>Meta Key</label>
			</div>
			<div class="field">
				<label>Meta Value</label>
			</div>
		</div>
	@endif

	<div class="two fields">

		<div class="field{{ $errors->has('meta_value') ? ' error' : '' }}">
			{{ $meta->value }}
		</div>

		<div class="field{{ $errors->has('meta_value') ? ' error' : '' }}">
			<input type="text" name="meta_value[{{$meta->id}}]" placeholder="Meta Value" value="{{ $meta->value }}">
		</div>

		<button type="button" class="ui right floated negative icon button basic remove-row">
			<i class="remove icon"></i>
		</button>
	</div>
@endforeach
{{--
<div class="two fields">
	<div class="field{{ $errors->has('meta_key') ? ' error' : '' }}">
		<input type="text" name="meta_key[]" placeholder="Meta Key" value="">
	</div>
	<div class="field{{ $errors->has('meta_value') ? ' error' : '' }}">
		<input type="text" name="meta_value[]" placeholder="Meta Value" value="">
	</div>
	<button type="button" class="ui right floated positive icon button basic add-row">
		<i class="add icon"></i>
	</button>
</div>
 --}}