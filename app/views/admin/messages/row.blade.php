<tr class="thread-row{{ !$message->read ? ' unread' : '' }}" data-id="{{ $message->id }}" data-all="1" data-read="{{ $message->read }}" data-title="{{{ $message->subject }}}">

	<td class="icon">
		<input type="checkbox" name="messages[]" value="{{ $message->id }}" tabindex="1">
	</td>

	<td class="icon">
		<img src="{{ $skin }}icons/topic{{ !$message->read ? '_unread' : '' }}.png">
	</td>
	
	<td class="from" style="width:25%">
		@if ( $message->user->id )
		<a href="{{ $message->user->url }}">{{{ $message->user->name }}}</a>
		@else
		<a href="mailto:{{ $message->email }}">{{{ $message->name }}}</a>
		@endif
	</td>

	<td class="topic">
		<div class="thread-preview">
		@if ( !$message->read )
		<a href="{{ $message->url }}"><img src="{{ $skin }}icons/newest_reply.png"></a>
		@endif
		
		<a href="{{ $message->url }}">{{{ $message->subject }}}</a>
		- {{ BBCode::simplify($message->message) }}
		</div>
	</td>

	<td class="lastpost message nowrap">
		{{ Helpers::date_string($message->created_at, 1) }}
	</td>
</tr>
