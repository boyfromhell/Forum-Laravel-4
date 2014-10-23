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

	<table class="table table-hover">
	<thead>
	<tr>
		<th class="icon">&nbsp;</th>
		<th class="icon">&nbsp;</th>
		<th style="width:25%">{{ $folder == 'sent' ? 'To' : 'From' }}</th>
		<th>Subject</th>
		<th class="lastpost">Date</th>
	</tr>
	<tr class="subheading messages">
		<td colspan="5">
		<div class="pull-left">
			Select:
			<a href="" onclick="select_threads('all', 1); return false">All</a>,
			<a href="" onclick="select_threads('all', 0); return false">None</a>@if ( $folder != 'sent' ),
			<a href="" onclick="select_threads('read', 1); return false">Read</a>,
			<a href="" onclick="select_threads('read', 0); return false">Unread</a> @endif
		</div>
		<div class="btn-group btn-group-md pull-right">
		@if ( $folder == 'inbox' )
			<button type="submit" name="archive" class="btn btn-default">
				<span class="glyphicon glyphicon-save"></span> Archive
			</button>
		@elseif ( $folder == 'archived' )
			<button type="submit" name="unarchive" class="btn btn-default">
				<span class="glyphicon glyphicon-inbox"></span> Move to Inbox
			</button>
		@endif
			<input id="delete-multiple" type="submit" name="delete" class="btn btn-danger" value="Delete" data-item="thread" data-action="Delete">
		
		@if ( $folder != 'sent' )
			<input type="submit" name="read" value="Mark Read" class="btn btn-default">
			<input type="submit" name="unread" value="Mark Unread" class="btn btn-default">
		@endif
		</div>
		<div class="clearfix"></div>
		</td>
	</tr>
	</thead>
	<tbody>
	@if ( count($threads) > 0 )
	@foreach ( $threads as $thread )
		@include ('messages.thread_row', ['thread_mode' => 'folder'])
	@endforeach
	@else
	<tr>
		<td colspan="5">
		<p class="empty">
		You have no messages in this folder
		</p>
		</td>
	</tr>
	@endif
	</tbody>
	</table>

</div>

</form>

@stop
