<style type="text/css">
.vote-bar {
	border: 1px solid #555;
	border-radius: 3px;
	float: left;
	background: #ccc;
	min-width: 10px;
	height: 16px;
	margin: 5px 0;
}
</style>

@if ( $poll->id )

<form method="post" action="">
<table class="layout" cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
	<td class="middle"><center><br>
		<b>{{{ $poll->question }}}</b>
	
		<table class="layout" cellpadding="0" cellspacing="0" border="0" style="margin:10px 0px;">
@if ( ! $me->id || Input::has('results') || $choices = $me->votedIn($poll->id) )

@foreach( $poll->options as $option )
		<tr>
			<td>
	@if( in_array($option->id, $choices) )
		<b>{{{ $option->content }}}</b>
	@else
		{{{ $option->content }}}
	@endif
			</td>
			<td style="padding:1px 0 1px 10px">
				<div class="vote-bar" style="width:{{ $option->width }}px"></div>
			</td>
			<td style="padding-left:10px">
				<b>{{ $option->percent }}%</b>
			</td>
			<td style="padding-left:10px">
	@if (( $me->id && $poll->is_public ) || $me->is_moderator )
				[ <a href="/poll-results/{{ $poll->id }}">{{ $option->total_votes }}</a> ]
	@else
				[ {{ $option->total_votes }} ]
	@endif
			</td>
		</tr>
@endforeach
		</table>

	<b>Total Votes: {{ $poll->total_votes }}</b><br>
	<small><a href="{{ $topic->url }}">Place vote</a></small>

@else

@foreach( $poll->options as $option )
		<tr>
			<td>
			<label><input tabindex="3" type="{{ $poll->type }}" name="voteopt[]" value="{{ $option->id }}" style="margin-right:10px">
			{{{ $option->content }}}</label>
			</td>
		</tr>
@endforeach
		</table>

		@if( $poll->max_options > 1 )
		<small><i>Select up to {{ $poll->max_options }} options</i></small><br>
		@endif

		<input tabindex="3" type="submit" name="voted" value="Submit Vote"><br>
		<small><a href="?results">View results</a></small>

		<br><br>

@endif
	</center></td>
</tr>

</table>
</form>

@endif
