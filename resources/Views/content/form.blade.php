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