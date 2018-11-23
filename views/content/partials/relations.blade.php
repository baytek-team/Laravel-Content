<h4 class="ui horizontal divider header">
	<i class="users icon"></i>
	{{ ___('Relations') }}
</h4>

<table class="ui very basic table">
	<tbody>
		@foreach($content->relations as $relation)
			@if($relation->relation_type_id && $relation->relation_id)
			<tr>
				<td>
                    <a href="{{ route('content.list', $relation->relation_type_id) }}">
                        {{ content($relation->relation_type_id)->key }}
                    </a>
                </td>
				<td class="right aligned">
                    <a href="{{ route('content.list', $relation->relation_id) }}">
                        {{ content($relation->relation_id)->key }}
                    </a>
                </td>
			</tr>
			@endif
		@endforeach
	</tbody>
</table>
