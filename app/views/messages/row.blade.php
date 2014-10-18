<div class="panel panel-primary">

	<div class="panel-heading">
		<div class="pull-left">
			{{ $message->date }}<a name="{{ $message->id }}"></a>
		</div>
		<div class="pull-right">
			#{{ $message->count }}
		</div>
		<div class="clearfix"></div>
	</div>

	<div class="recipients">
		To: 
		@foreach ( $message->to as $count => $user )
			<a href="{{ $user->url }}">{{{ $user->name }}}</a>{{ $count < count($message->users)-1 ? ', ' : '' }}
		@endforeach
	</div>

	@include ('users.row', ['content_id' => $message->id, 'user' => $message->from])

	<div class="panel-body" id="pt{$message->id}">

		<div id="post{$message->id}">
	
			{{ BBCode::parse($message->content) }}

		</div>
	
		<div class="pull-right">
			<a href="/messages/compose?p={{ $message->id }}" class="btn btn-default btn-xs">Quote</a>
			<a href="/delete.php?pm={{ $message->id }}" class="btn btn-danger btn-xs">x</a>
		</div>

	</div>

	@if ( $message->from->sig && $message->from->attach_sig )
	<div class="panel-footer sig">
		{{ BBCode::parse($message->from->sig) }}
	</div>
	@endif

	@include ('posts.attachments', ['post' => $message])

</div>

