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
	{{-- <button type="submit" class="ui right floated green button">
	    Add Metadata
	</button> --}}
</h4>
@endif

@foreach($content->meta as $meta)
{{-- ALMOST A TABLE --}}
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
	<input type="hidden" name="id[]" value="{{ $meta->id }}">
	<div class="field{{ $errors->has('key') ? ' error' : '' }}">
		<input type="text" id="meta_key_{{ $meta->key }}" name="key[]" placeholder="Meta Key" value="{{ $meta->key }}">
	</div>
	<div class="field{{ $errors->has('content') ? ' error' : '' }}">
		<textarea id="meta_value_{{ $meta->key }}" name="value[]" rows="1" placeholder="Meta Value">{{ $meta->value }}</textarea>
	</div>
	<button type="submit" class="ui right floated red button">
	    X
	</button>
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
		{{-- @if($relation == $content->relations->first())<label>Content</label>@endif --}}
		<!-- <input type="text" id="relation_key_{{ $relation->content_id }}" name="key[]" placeholder="relation Key" value="{{ $relation->content->title }}"> -->
		<select id="" name="content" class="ui dropdown">
			@foreach($contents as $item)
			<option value="{{ $item->id }}"@if($relation->content_id == $item->id) selected="selected"@endif>{{ $item->title }}</option>
			@endforeach
		</select>
	</div>
	<div class="field{{ $errors->has('content') ? ' error' : '' }}">
		{{-- @if($relation == $content->relations->first())<label>Content</label>@endif --}}
		<!-- <textarea id="relation_value_{{ $relation->relation_id }}" name="value[]" rows="1" placeholder="relation Value">{{ $relation->relation->title }}</textarea> -->
		<select id="" name="relation" class="ui dropdown">
			@foreach($contents as $item)
			<option value="{{ $item->id }}"@if($relation->relation_id == $item->id) selected="selected"@endif>{{ $item->title }}</option>
			@endforeach
		</select>
	</div>
	<div class="field{{ $errors->has('content') ? ' error' : '' }}">
		{{-- @if($relation == $content->relations->first())<label>Relation Type</label>@endif --}}
		<!-- <textarea id="relation_value_{{ $relation->relation_type_id }}" name="value[]" rows="1" placeholder="relation Value">{{ $relation->relationType->title }}</textarea> -->
		<select id="" name="relation_type" class="ui dropdown">
			@foreach($contents as $item)
			<option value="{{ $item->id }}"@if($relation->relation_type_id == $item->id) selected="selected"@endif>{{ $item->title }}</option>
			@endforeach
		</select>
	</div>
	<button type="submit" class="ui right floated red button">
	    X
	</button>
</div>
@endforeach