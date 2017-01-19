<div class="two fields">
    <div class="eight wide field{{ $errors->has('title') ? ' error' : '' }}">
        <label for="title">Title</label>
        <input type="text" id="title" name="title" placeholder="Title" value="{{ old('title', $content->title) }}">
    </div>
    <div class="eight wide field{{ $errors->has('content') ? ' error' : '' }}">
        <label for="content">Content</label>
        <textarea id="content" name="content" placeholder="Content">{{ old('content', $content->content) }}</textarea>
    </div>
</div>
