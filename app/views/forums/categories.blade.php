@foreach ( $categories as $i => $category )

<div class="panel panel-primary">

	<div class="panel-heading">{{{ $category->name }}}</div>

	<table class="table">
@if ( $i == 0 )
	<thead>
	<tr>
		<th class="icon wide">&nbsp;</th>
		<th style="width:40%">Forum</th>
		<th>Last Post</th>
		<th class="posts">Topics</th>
		<th class="posts">Posts</th>
	</tr>
	</thead>
	<tbody>
@endif
	@foreach ( $category->forums as $forum )
		@include ('forums.row')
	@endforeach
	</tbody>
	</table>

</div>
@endforeach
