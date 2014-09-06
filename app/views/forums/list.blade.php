<div class="welcome wide no-margin">
	<div class="table-header">
		<table class="table2" cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<th class="icon wide">&nbsp;</th>
			<th style="width:40%">Forum</th>
			<th>Last Post</th>
			<th class="posts">Topics</th>
			<th class="posts">Posts</th>
			<th style="width:10px">&nbsp;</th>
		</tr>
		</table>
	</div>
</div>

<div class="welcome wide">

	<div class="header">Sub-Forums</div>

	<div class="body">
	<table class="table2" cellpadding="0" cellspacing="0" border="0" width="100%">

	@foreach ( $forums as $forum )
		@include ('forums.row')
	@endforeach
	</table>
	</div>

</div>
