@if ( $shout->show_date )
<tr>
	<th class="date" colspan="4" name="shouts{{ Helpers::local_date('md', $shout->created_at) }}">{{ Helpers::local_date('F j', $shout->created_at) }}</th>
</tr>
@endif
<tr id="shout{{ $shout->id }}">
	<td width="80" valign="top">
	<small>{{ Helpers::local_date('g:i&\n\b\s\p;a', $shout->created_at) }}</small></td>
@if ( substr($shout->message, 0, 4) == "/me " )
	<td colspan="2" valign="top">* <a href="{{ $shout->user->url }}"><b>{{{ $shout->user->name }}}</b></a> {{{ substr($shout->message, 4) }}} *</td>
@else
	<td width="150" valign="top"><a href="{{ $shout->user->url }}"><b>{{{ $shout->user->name }}}</b></a>
		@if ( $shout->at_me )
			<img src="/images/smileys/banana.gif">
		@endif
	</td>
	<td>{{ $shout->message }}</td>
@endif
	
	<td width="17" valign="top">
@if ( $shout->user_id == $me->id && strtotime($shout->created_at) > gmmktime()+(($me->tz-1)*3600) )
	<a href="" onclick="deleteShout({{ $shout->id }}); return false"><img src="{{ $skin }}icons/tinyx.png" alt="x"></a>
@else
	&nbsp;
@endif
	</td>
</tr>
