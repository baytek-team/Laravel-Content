<div class="field{{ $errors->has('key') ? ' error' : '' }}">
	<label for="key">Key</label>
	<input type="text" id="key" name="key" placeholder="Key" value="{{ old('key', $content->key) }}">
</div>
<div class="field{{ $errors->has('title') ? ' error' : '' }}">
	<label for="title">Title</label>
	<input type="text" id="title" name="title" placeholder="Title" value="{{ old('title', $content->title) }}">
</div>
<div class="field{{ $errors->has('content') ? ' error' : '' }}">
	<label for="content">Content</label>
	<textarea id="content" name="content" placeholder="Content">{{ old('content', $content->content) }}</textarea>
</div>


<!-- <button type="button" class="ui left floated basic positive button add-row">
	Add Metadata
</button> -->
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
	<div class="field{{ $errors->has('meta_key') ? ' error' : '' }}">
		<input type="text" name="meta_key[{{$meta->id}}]" placeholder="Meta Key" value="{{ $meta->key }}">
	</div>
	<div class="field{{ $errors->has('meta_value') ? ' error' : '' }}">
		<input type="text" name="meta_value[{{$meta->id}}]" placeholder="Meta Value" value="{{ $meta->value }}">
	</div>
	<button type="button" class="ui right floated negative icon button basic remove-row">
		<i class="remove icon"></i>
	</button>
</div>
@endforeach

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

<!-- <button type="button" class="ui left floated basic positive button add-row">
	Add Relationship
</button> -->
<h4 class="ui horizontal divider header">
	<i class="users icon"></i>
	Relationships
</h4>

<input type="hidden" name="relation_ids" value="{{ json_encode($content->relations->pluck('id')) }}">

@foreach($content->relations as $relation)

@if($relation == $content->relations->first())
<div class="three fields">
	<div class="field">
		<label>Content</label>
	</div>
	<div class="field">
		<label>Relation Type</label>
	</div>
	<div class="field">
		<label>Relation</label>
	</div>
</div>
@endif

<div class="three fields relationship-row">
	<div class="field{{ $errors->has('key') ? ' error' : '' }}">
		<select name="content_id[{{$relation->id}}]" class="ui dropdown disabled">
			@foreach($contents as $item)
			<option value="{{ $item->id }}"@if($content->id == $item->id) selected="selected"@endif>{{ $item->title }}</option>
			@endforeach
		</select>
	</div>
	<div class="field{{ $errors->has('content') ? ' error' : '' }}">
		<select name="relation_type_id[{{$relation->id}}]" class="ui dropdown relation-type">
			@foreach($relationTypes as $item)
			<option value="{{ $item->id }}"@if($relation->relation_type_id == $item->id) selected="selected"@endif>{{ $item->title }}</option>
			@endforeach
		</select>
	</div>
	<div class="field{{ $errors->has('content') ? ' error' : '' }}">
		<select name="relation_id[{{$relation->id}}]" class="ui dropdown relation">
			@foreach($contents as $item)
			<option value="{{ $item->id }}"@if($relation->relation_id == $item->id) selected="selected"@endif>{{ $item->title }}</option>
			@endforeach
		</select>
	</div>
	<button type="button" class="ui right floated negative icon button basic remove-row">
		<i class="remove icon"></i>
	</button>
</div>
@endforeach

<div class="three fields relationship-row">
	<div class="field">
		<select name="content_id[]" class="ui fluid dropdown disabled">
			<option value="">Select Content</option>
			@foreach($contents as $item)
			<option value="{{ $item->id }}"@if($content->id == $item->id) selected="selected"@endif>{{ $item->title }}</option>
			@endforeach
		</select>
	</div>
	<div class="field">
		<select name="relation_type_id[]" class="ui fluid dropdown relation-type">
			<option value="">Select Relationship Type</option>
			@foreach($relationTypes as $item)
			<option value="{{ $item->id }}">{{ $item->title }}</option>
			@endforeach
		</select>
	</div>
	<div class="field">
		<select name="relation_id[]" class="ui fluid dropdown relation">
			<option value="">Select Related Content</option>
			@foreach($contents as $item)
			<option value="{{ $item->id }}">{{ $item->title }}</option>
			@endforeach
		</select>
	</div>
	<button type="button" class="ui right floated positive icon button basic add-row">
		<i class="add icon"></i>
	</button>
</div>

@section('scripts')
@php
$items = [];
@endphp
@foreach($relationTypes as $item)
	@php
		if($item->key == 'parent-id') {
			$item->children = $contents;
		}
		else {
			$item->children = $item->childrenOf($item->key)->get();
		}
		array_push($items, $item);
	@endphp
@endforeach
<script>

;+function($) {
	var options = {!! json_encode($items) !!};

	$('.relation-type.dropdown').on('change', function(e) {
		var dropdown = this;
		var optionValue = $(this).find('select').val();

		var posibilities = ''; //= [];

		options.forEach(function(item, index){
			if(item.id == optionValue) {
				item.children.forEach(function(option) {
					posibilities += '<option value="' + option.id + '">' + option.title + '</option>';
				});

				$(dropdown).parents('.relationship-row').find('.relation select').html(posibilities);

				$(dropdown).parents('.relationship-row').find('.relation').dropdown('refresh');

				// $(dropdown).parents('.relationship-row').find('.relation').api({
				// 	response: {
				// 		success: true,
				// 		results: posibilities
				// 	}
				// });
			}
		});
	});
}(jQuery);
</script>@endsection
