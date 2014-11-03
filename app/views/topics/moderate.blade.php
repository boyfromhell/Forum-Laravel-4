<div class="btn-group btn-group-md pull-left">
	<a href="/move-topic/{{ $topic->id }}" class="btn btn-default">
		<span class="glyphicon glyphicon-arrow-right"></span> Move
	</a>
@if ( $topic->is_locked )
	<a href="/unlock-topic/{{ $topic->id }}" class="btn btn-default">
		<span class="glyphicon glyphicon-unlock"></span> Unlock
	</a>
@else
	<a href="/lock-topic/{{ $topic->id }}" class="btn btn-default">
		<span class="glyphicon glyphicon-lock"></span> Lock
	</a>
@endif
{{--
	<a href="/split-topic/{{ $topic->id }}" class="btn btn-default">
		<span class="glyphicon glyphicon-resize-full"></span> Split
	</a>
--}}
	<a href="/delete-topic/{{ $topic->id }}" class="btn btn-danger">
		<span class="glyphicon glyphicon-remove"></span> Delete
	</a>
</div>

<div class="clearfix"></div>
