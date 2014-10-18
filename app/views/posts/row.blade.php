@include ('users.menu', ['content_id' => $post->id, 'user' => $post->user])

<div class="panel panel-primary">

	@if ( $post->showhr > 0 && !$post->ignored )
	<div class="table-header">
		<table class="table">
		<tr>
			<th class="soft">
			@if ( $post->smiley )
				{{ display_smiley($post->smiley) }}
			@endif
			@if ( $post->showhr == 2 )
				<b>{{{ $post->subject }}}</b>
			@endif
			</th>
		</tr>
		</table>
	</div>
	@endif

	<div class="panel-heading">
		<div class="pull-left">
			<a name="{{ $post->id }}"></a>{{ $post->date }}
		</div>
		<div class="pull-right">
			<a href="{{ $post->url }}">#{{ $post->count }}</a>
		@if ( $me->is_moderator )
			<a href="/lookup.php?p={{ $post->id }}">IP</a>
		@endif
			<a href="/forum/report?p={{ $post->id }}"><img src="{{ $skin }}icons/report.png" alt="!" title="Report post" style="vertical-align:top"></a>
		</div>
		
		<div class="clearfix"></div>
	</div>

	@if ( !$post->ignored )
	@include ('users.row', ['content_id' => $post->id, 'user' => $post->user])
	@endif
	
	<div class="panel-body" id="pt{{ $post->id }}">
	
@if ( $post->ignored )
	<div id="post{{ $post->id }}" class="text-center">
		<a href="{{ $post->user->url }}">{{{ $post->user->name }}}</a> is on your ignore list
	</div>
@else

	<div id="post{{ $post->id }}">
	
	@include ('posts.body')

	</div>
	
	<div class="pull-right">
	@if ( $me->id )
		@if ( !$topic->status )
			<a href="/quote-post/{{ $post->id }}" class="btn btn-primary btn-xs">Quote</a>
		@endif
	@endif
	@if ( $me->id && ( $me->id == $post->user_id || $me->is_moderator ) )
		@if ( !$topic->status || $me->is_moderator )
			<a href="/edit-post/{{ $post->id }}" onClick="parangi.quickEdit({{ $post->id }}, 'edit'); return false" class="btn btn-default btn-xs">Edit</a>
			<a href="/delete-post/{{ $post->id }}" class="btn btn-danger btn-xs">x</a>
		@endif
	@endif
	
	</div>

	{{-- @todo edits --}}
	@if ( $post->edit_count > 0 && $post->edit_time > $post->time+300 && $post->edit_user_id != 2 )
		{{-- <div class='editmsg'>
		$edittime = datestring($edittime, 1);
		$editsql = "SELECT name FROM users WHERE id = '$editauth'
		$editres = query($editsql);
		list( $editauthor ) = mysql_fetch_array($editres);
		$editauthor = stripslashes($editauthor); 
		echo "Last edited by <a href="/users/".$editauth."/".makeurl($editauthor,1)."">".$editauthor."</a> : $edittime.
		if( $edits > 1 ) {
			echo " Edited $edits times total
		}
		</div> --}}
	@endif
	
	</div>

	@if ( $post->user->sig && $post->user->attach_sig && $post->signature )
	<div class="panel-footer sig">
		{{ BBCode::parse($post->user->sig) }}
	</div>
	@endif
	
	@include ('posts.attachments')

@endif

</div>

