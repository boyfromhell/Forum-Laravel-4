@if ( $poll->id )

<div class="well text-center">

	<form method="post" action="">

	<b>{{{ $poll->question }}}</b>

	<div class="row">
	<div class="col-md-8 col-lg-6 col-md-offset-2 col-lg-offset-3">
	<table class="table no-border">
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
		<td width="60%">
			<div class="progress" style="width:{{ $option->width }}%">
				<div class="progress-bar" style="width:100%">
			</div>
		</td>
		<td>
			<b>{{ $option->percent }}%</b>
		</td>
		<td class="nowrap">
	@if (( $me->id && $poll->is_public ) || $me->is_moderator )
			[ <a href="/poll-results/{{ $poll->id }}">{{ $option->total_votes }}</a> ]
	@else
			[ {{ $option->total_votes }} ]
	@endif
		</td>
	</tr>
@endforeach
	</table>
	</div>
	</div>

	<b>Total Votes: {{ $poll->total_votes }}</b><br>
	<small><a href="{{ $topic->url }}">Place vote</a></small>

@else

@foreach( $poll->options as $option )
	<tr>
		<td>
		<label class="{{ $poll->type }}-inline">
		<input type="{{ $poll->type }}" name="voteopt[]" value="{{ $option->id }}">
		{{{ $option->content }}}</label>
		</td>
	</tr>
@endforeach
	</table>
	</div>
	</div>

	@if( $poll->max_options > 1 )
	<small><i>Select up to {{ $poll->max_options }} options</i></small><br>
	@endif

	<input type="submit" name="voted" value="Submit Vote"><br>
	<small><a href="?results">View results</a></small>

	<br><br>

@endif
</form>

</div>

@endif
