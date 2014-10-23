@extends('layout')

@section('buttons')
<div class="pull-right">
	{{ $topics->links() }}
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

<form method="post" action="/subscriptions">

<div class="panel panel-primary">

	<div class="panel-heading">Topic Subscriptions</div>

	<table class="table">
	<thead>
	<tr>
		<th class="icon">&nbsp;</th>
		<th class="icon">&nbsp;</th>
		<th class="icon">&nbsp;</th>
		<th style="min-width:30%">Topics</th>
		<th class="hidden-xs">Forum</th>
		<th class="hidden-xs">Last Post</th>
		<th class="posts hidden-xs">Replies</th>
		<th class="posts hidden-xs">Views</th>
	</tr>
	<tr class="subheading messages">
		<td colspan="8">
		<div class="pull-left">
			Select:
			<a href="" onclick="select_threads('all', 1); return false">All</a>,
			<a href="" onclick="select_threads('all', 0); return false">None</a>,
			<a href="" onclick="select_threads('read', 1); return false">Read</a>,
			<a href="" onclick="select_threads('read', 0); return false">Unread</a>
		</div>
		<div class="btn-group btn-group-md pull-right">
			<button id="delete-multiple" type="submit" name="unsubscribe" class="btn btn-danger" data-item="topic" data-action="Unsubscribe from">
				Unsubscribe
			</button>
		</div>
		<div class="clearfix"></div>
		</td>
	</tr>
	</thead>
	<tbody>
	@if ( count($topics) > 0 )
	@foreach ( $topics as $topic )
		@include ('topics.row', ['topic_mode' => 'subscription'])
	@endforeach
	@else
	<tr>
		<td colspan="8">
		<p class="empty">You are not subscribed to any topics</p>
		</td>
	</tr>
	@endif
	</tbody>
	</table>

</div>

</form>

@stop
