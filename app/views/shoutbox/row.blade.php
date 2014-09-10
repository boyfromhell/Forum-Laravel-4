@if ( $shout->show_date )
<tr>
	<th class="date" colspan="4" name="shouts{{ $shout->get_date('md') }}">{{ $shout->get_date('F j') }}</th>
</tr>
@endif
<tr id="shout{{ $shout->id }}">
	<td width="80" valign="top">
	<small>{{ $shout->get_date('g:i&\n\b\s\p;a') }}</small></td>
@if ( substr($shout->message, 0, 4) == "/me " )
	<td colspan="2" style="padding:2px 0" valign="top">* <a target="_top" href="{{ $shout->user->url }}" style="text-decoration:none; font-weight:bold">{{{ $shout->user->name }}}</a> {{{ substr($shout->message, 4) }}} *</td>
@else
	<td width="150" style="padding:2px 0" valign="top"><a target="_top" href="{{ $shout->user->url }}" style="text-decoration:none; font-weight:bold">{{{ $shout->user->name }}}</a>
		@if ( $shout->at_me )
			<img src="/images/smileys/banana.gif" style="margin-bottom:-3px">
		@endif
	</td>
	<td class="shoutbox">{{{ $shout->message }}}</td>
@endif
	
	<td width="17" style="padding:3px 0 3px 5px;" valign="top">
@if ( $shout->user_id == $me->id && $shout->time > $gmt+(($me->tz-1)*3600) )
	<a href="" onclick="deleteShout({{ $shout->id }}); return false" style="text-decoration:none"><img src="{{ $skin }}icons/tinyx.png" alt="x"></a>
@else
	&nbsp;
@endif
	</td>
</tr>
