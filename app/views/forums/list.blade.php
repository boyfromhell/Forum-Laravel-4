<div class="panel panel-primary">

	<div class="panel-heading">Sub-Forums</div>

	<table class="table">
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
	@foreach ( $forums as $forum )
		@include ('forums.row')
	@endforeach
	</tbody>
	</table>

</div>
