<div class="btn-group btn-group-md pull-left">
	<a href="/admin/topics.php?mode=move&t={{ $topic->id }}" class="btn btn-default">
		<span class="glyphicon glyphicon-arrow-right"></span> Move
	</a>
@if ( $topic->status == 1 )
	<a href="/admin/topics.php?mode=unlock&t={{ $topic->id }}" class="btn btn-default">
		<span class="glyphicon glyphicon-unlock"></span> Unlock
	</a>
@else
	<a href="/admin/topics.php?mode=lock&t={{ $topic->id }}" class="btn btn-default">
		<span class="glyphicon glyphicon-lock"></span> Lock
	</a>
@endif
	<a href="/admin/topics.php?mode=split&t={{ $topic->id }}" class="btn btn-default">
		<span class="glyphicon glyphicon-resize-full"></span> Split
	</a>
	<a href="/admin/topics.php?mode=delete&t={{ $topic->id }}" class="btn btn-danger">
		<span class="glyphicon glyphicon-remove"></span> Delete
	</a>
</div>

<div class="clearfix"></div>
