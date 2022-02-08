@php
	$languages = config('language.locales');
	if(($key = array_search($content->language, $languages)) !== false) {
	    unset($languages[$key]);
	}
@endphp

<div class="field{{ $errors->has('language') ? ' error' : '' }}">
	<label for="language">{{ __('Language') }}</label>
	<select name="language" class="ui dropdown">
		@foreach($languages as $locale)
			<option value="{{ $locale }}">{{ $locale }}</option>
		@endforeach
	</select>
</div>

<div class="field{{ $errors->has('title') ? ' error' : '' }}">
	<label for="title">{{ __('Title') }}</label>
	<p class="">{{ $content->title }}</p>
	<input type="text" id="title" name="title" placeholder="{{ __('Title') }}" value="{{ old('title', $content->title) }}">
</div>


<div class="field{{ $errors->has('content') ? ' error' : '' }}">
	<label for="content">{{ __('Content') }}</label>
	<p>{{ $content->content }}</p>
	<textarea id="content" name="content" placeholder="{{ __('Content') }}">{{ old('content', $content->content) }}</textarea>
</div>

<h4 class="ui horizontal divider header">
	<i class="tags icon"></i>
	{{ __('Metadata') }}
</h4>

<input type="hidden" name="meta_ids" value="{{ json_encode($content->meta->pluck('id')) }}">

@foreach($content->meta as $meta)
	@if($meta == $content->meta->first())
		<div class="two fields">
			<div class="field">
				<label>{{ __('Meta Value') }} ({{ $content->language }})</label>
			</div>
			<div class="field">
				<label>{{ __('Meta Value (Translation)') }}</label>
			</div>
		</div>
	@endif

	<div class="ui two fields vertical segment">
		<div class="field{{ $errors->has('meta_value') ? ' error' : '' }}">
			{{ $meta->value }}
		</div>

		<div class="field{{ $errors->has('meta_value') ? ' error' : '' }}">
			<input type="text" name="meta_value[{{$meta->id}}]" placeholder="{{ __('Meta Value') }}" value="{{ $meta->value }}">
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