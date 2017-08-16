<h4 class="ui horizontal divider header">
	<i class="users icon"></i>
	{{ ___('Relations') }}
</h4>

<table class="ui very basic table">
	<tbody>
		@foreach($content->relations as $relation)
			@if($relation->relation_type_id && $relation->relation_id)
			<tr>
				<td>{{ $content->find($relation->relation_type_id)->key }}</td>
				<td>{{ $content->find($relation->relation_id)->key }}</td>
			</tr>
			@endif
		@endforeach
	</tbody>
</table>