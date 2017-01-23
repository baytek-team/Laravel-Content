<div class="field{{ $errors->has('title') ? ' error' : '' }}">
	<label for="title">Title</label>
	<input type="text" id="title" name="title" placeholder="Title" value="{{ old('title', $content->title) }}">
</div>
<div class="field{{ $errors->has('content') ? ' error' : '' }}">
	<label for="content">Content</label>
	<textarea id="content" name="content" placeholder="Content">{{ old('content', $content->content) }}</textarea>
</div>

@if($content->meta->count())
<h4 class="ui horizontal divider header">
	<i class="tags icon"></i>
	Metadata
</h4>
@endif

@foreach($content->meta as $meta)
<div class="two fields">
	<input type="hidden" name="id[]" value="{{ $meta->id }}">
	<div class="eight wide field{{ $errors->has('key') ? ' error' : '' }}">
		<label for="meta_key_{{ $meta->key }}">Meta Key</label>
		<input type="text" id="meta_key_{{ $meta->key }}" name="key[]" placeholder="Meta Key" value="{{ $meta->key }}">
	</div>
	<div class="eight wide field{{ $errors->has('content') ? ' error' : '' }}">
		<label for="meta_value_{{ $meta->key }}">Meta Value</label>
		<textarea id="meta_value_{{ $meta->key }}" name="value[]" rows="1" placeholder="Meta Value">{{ $meta->value }}</textarea>
	</div>
</div>
@endforeach

@if($content->relations->count())
<h4 class="ui horizontal divider header">
	<i class="users icon"></i>
	Relationships
</h4>
@endif

@foreach($content->relations as $relation)
<div class="three fields">
	<input type="hidden" name="id[]" value="{{ $relation->id }}">
	<div class="field{{ $errors->has('key') ? ' error' : '' }}">
		<label for="relation_key_{{ $relation->content_id }}">Content</label>
		<!-- <input type="text" id="relation_key_{{ $relation->content_id }}" name="key[]" placeholder="relation Key" value="{{ $relation->content->title }}"> -->
		<select id="" name="content" class="ui dropdown">
			@foreach($contents as $content)
			<option value="{{ $content->id }}"@if($relation->content_id == $content->id) selected="selected"@endif>{{ $content->title }}</option>
			@endforeach
		</select>
	</div>
	<div class="field{{ $errors->has('content') ? ' error' : '' }}">
		<label for="relation_value_{{ $relation->relation_id }}">Relation</label>
		<!-- <textarea id="relation_value_{{ $relation->relation_id }}" name="value[]" rows="1" placeholder="relation Value">{{ $relation->relation->title }}</textarea> -->
		<select id="" name="relation" class="ui dropdown">
			@foreach($contents as $content)
			<option value="{{ $content->id }}"@if($relation->relation_id == $content->id) selected="selected"@endif>{{ $content->title }}</option>
			@endforeach
		</select>
	</div>
	<div class="field{{ $errors->has('content') ? ' error' : '' }}">
		<label for="relation_value_{{ $relation->relation_type_id }}">Relation Type</label>
		<!-- <textarea id="relation_value_{{ $relation->relation_type_id }}" name="value[]" rows="1" placeholder="relation Value">{{ $relation->relationType->title }}</textarea> -->
		<select id="" name="relation_type" class="ui dropdown">
			@foreach($contents as $content)
			<option value="{{ $content->id }}"@if($relation->relation_type_id == $content->id) selected="selected"@endif>{{ $content->title }}</option>
			@endforeach
		</select>
	</div>
</div>
@endforeach