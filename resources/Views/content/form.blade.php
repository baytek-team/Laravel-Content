<div class="field{{ $errors->has('title') ? ' error' : '' }}">
    <label for="title">Title</label>
    <input type="text" id="title" name="title" placeholder="Title" value="{{ old('title', $content->title) }}">
</div>
<div class="field{{ $errors->has('content') ? ' error' : '' }}">
    <label for="content">Content</label>
    <textarea id="content" name="content" placeholder="Content">{{ old('content', $content->content) }}</textarea>
</div>

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

@foreach($content->relations as $relation)
<div class="two fields">
	<input type="hidden" name="id[]" value="{{ $relation->id }}">
    <div class="eight wide field{{ $errors->has('key') ? ' error' : '' }}">
        <label for="relation_key_{{ $relation->key }}">relation Key</label>
        <input type="text" id="relation_key_{{ $relation->key }}" name="key[]" placeholder="relation Key" value="{{ $relation->key }}">
    </div>
    <div class="eight wide field{{ $errors->has('content') ? ' error' : '' }}">
        <label for="relation_value_{{ $relation->key }}">relation Value</label>
        <textarea id="relation_value_{{ $relation->key }}" name="value[]" rows="1" placeholder="relation Value">{{ $relation->value }}</textarea>
    </div>
</div>
@endforeach