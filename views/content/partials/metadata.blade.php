<h4 class="ui horizontal divider header">
	<i class="tags icon"></i>
	{{ ___('Metadata') }}
</h4>

<table class="ui very basic table">
	<tbody>
		@foreach($content->meta as $meta)
			<tr>
				<td>{{ $meta->key }}</td>
				<td>{{ $content->metadata($meta->key) }}</td>
			</tr>
		@endforeach
	</tbody>
</table>