@extends('layout')

@section('buttons')
<div class="pull-left">
	<a href="/messages/compose" class="btn btn-primary">Compose</a>
</div>
<div class="pull-right">
	{{ $threads->links() }}
</div>
@stop

@section('content')

<script type="text/javascript">
$(document).ready( function() {
	$('.thread-row .icon input').on('click', function(e) {
		e.stopPropagation();
		if( $(this).prop('checked') ) {
			$(this).closest('.thread-row').addClass('selected');
		} else {
			$(this).closest('.thread-row').removeClass('selected');
		}
	});
	$('.thread-row').on('click', function(e) {
		var $target = $(e.target);
		if( !$target.is('a') ) {
			var $check = $(this).find('.icon').find('input');
			if( $check.prop('checked') ) {
				$check.prop('checked', false);
				$(this).removeClass('selected');
			} else {
				$check.prop('checked', true);
				$(this).addClass('selected');
			}
		}
	});
});

function select_threads( critera, value ) {
	$('.thread-row').each( function() {
		if( $(this).data(critera) == value ) {
			$(this).find('.icon').find('input').prop('checked', true);
			$(this).addClass('selected');
		} else {
			$(this).find('.icon').find('input').prop('checked', false);
			$(this).removeClass('selected');
		}
	});
}
</script>

<form method="post" action="/messages/{{ $folder }}{{ $page > 1 ? '?page='.$page : '' }}">

<div class="panel panel-primary">

	<div class="panel-heading">{{ ucwords($folder) }}</div>

	<table class="table">
	<thead>
	<tr>
		<th class="icon">&nbsp;</th>
		<th class="icon">&nbsp;</th>
		<th style="width:25%">{{ $folder == 'sent' ? 'To' : 'From' }}</th>
		<th>Subject</th>
		<th class="lastpost">Date</th>
	</tr>
	</thead>
	<tbody>
	<tr class="subheading messages">
		<td colspan="3">
			Select:
			<a href="" onclick="select_threads('all', 1); return false">All</a>,
			<a href="" onclick="select_threads('all', 0); return false">None</a>@if ( $folder != 'sent' ),
			<a href="" onclick="select_threads('read', 1); return false">Read</a>,
			<a href="" onclick="select_threads('read', 0); return false">Unread</a> @endif
		</td>
		<td style="text-align:right" colspan="2">
		@if ( $folder == 'inbox' )
			<input type="submit" name="archive_messages" value="Archive" class="btn btn-default btn-md" tabindex="1">
		@elseif ( $folder == 'archived' )
			<input type="submit" name="unarchive_messages" class="btn btn-default btn-md" value="Move to Inbox">
		@endif
			<input id="delete-multiple" type="submit" name="delete_messages" class="btn btn-danger btn-md" value="Delete" data-item="thread" data-action="Delete">
		
		@if ( $folder != 'sent' )
			<input type="submit" name="read_messages" value="Mark Read" class="btn btn-default btn-md" tabindex="1">
			<input type="submit" name="unread_messages" value="Mark Unread" class="btn btn-default btn-md" tabindex="1">
		@endif
		</td>
	</tr>
	@if ( count($threads) > 0 )
	@foreach ( $threads as $thread )
		@include ('messages.thread_row', ['thread_mode' => 'folder'])
	@endforeach
	@else
	<div class="empty">
		You have no messages in this folder
	</div>
	@endif
	</tbody>
	</table>

</div>

</form>

@stop
