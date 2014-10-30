@extends('layout')

@section('buttons')
<div class="pull-right">
{{ $admin_messages->links() }}
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

function select_messages( critera, value ) {
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

@if ( count($reports) > 0 )
<div class="panel panel-primary">

	<div class="panel-heading">Flagged Posts</div>

	<table class="table">
	<thead>
		<tr>
			<th class="icon">&nbsp;</th>
			<th style="width:35%">Post</th>
			<th style="width:15%">Reported by</th>
			<th>Reason</th>
			<th class="posts">Admin</th>
		</tr>
	</thead>
	<tbody>
	@foreach( $reports as $report )
		<tr>
			<td class="icon">&nbsp;</td>
			<td style="width:35%"><a href="{{ $report->post->url }}">{{{ $report->post->topic->title }}}</a><br>
				by <a href="{{ $report->post->user->url }}">{{{ $report->post->user->name }}}</a></td>
			<td style="width:15%"><a href="{{ $report->user->url }}">{{{ $report->user->name }}}</a></td>
			<td>{{ $report->reason }}<br>
				{{ BBCode::parse($report->comments) }}
			</td>
			<td class="posts"><a class="action" href="" data-id="{{ $report->id }}" data-action="complete">Complete</a><br>
				<a class="action" href="" data-id="{{ $report->id }}" data-action="rejected">Reject</a>
			</td>
		</tr>
	@endforeach
	</tbody>
	</table>

</div>

<script type="text/javascript">
$('.action').click( function(e) {
	e.preventDefault();
	var $row = $(this).closest('tr');
	var send_data = {
		'id'     : $(this).data('id'),
		'action' : $(this).data('action')
	};
	$.post('/admin/handle-report', send_data, function(data) {
		$row.fadeOut('fast');
	}, 'json');
});
</script>
@endif

<form method="post" action="/admin/messages">

<div class="panel panel-primary">

	<div class="panel-heading">Contact Form Messages</div>

	<table class="table table-hover" cellpadding="0" cellspacing="0" border="0" width="100%">
	<thead>
	<tr>
		<th class="icon">&nbsp;</th>
		<th class="icon">&nbsp;</th>
		<th style="width:25%">From</th>
		<th>Subject</th>
		<th class="lastpost">Date</th>
	</tr>
	<tr class="subheading messages">
		<td colspan="5">
		<div class="pull-left">
			Select: 
			<a href="" onclick="select_messages('all', 1); return false">All</a>, 
			<a href="" onclick="select_messages('all', 0); return false">None</a>,
			<a href="" onclick="select_messages('read', 1); return false">Read</a>, 
			<a href="" onclick="select_messages('read', 0); return false">Unread</a>
		</div>
		<div class="btn-group btn-group-md pull-right">
			<button type="submit" name="archive" class="btn btn-default">
				<span class="glyphicon glyphicon-save"></span> Archive
			</button>
			<input id="delete-multiple" type="submit" name="delete" class="btn btn-danger" value="Delete" data-item="message" data-action="Delete">
			<input type="submit" name="read" value="Mark Read" class="btn btn-default">
			<input type="submit" name="unread" value="Mark Unread" class="btn btn-default">
		</div>	
		<div class="clearfix"></div>
		</td>
	</tr>
	</thead>
	<tbody>
	@if ( count($admin_messages) > 0 )
		@foreach ( $admin_messages as $message )
			@include ('admin.messages.row')
		@endforeach
	@else
	<tr>
		<td colspan="5">
		<p class="empty">
		There are no messages
		</p>
		</td>
	</tr>
	@endif
	</tbody>
	</table>
	
</div>

</form>

@stop

