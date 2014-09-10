<div class="smileys">

<table cellspacing="0" cellpadding="0" border="0">
@foreach ( $smileys as $k => $smiley )
	@if ( $k % 5 == 0 )
		<tr>
	@endif
	<td>
	<div class="emote" onclick="addtext(' {{{ $smiley->code }}} ', '', 0);" title=" {{{ $smiley->code }}} "><img src="/images/smileys/{{{ $smiley->file }}}" alt=" {{{ $smiley->code }}} "></div>
	</td>
	@if ( $k % 5 == 4 )
		</tr>
	@endif
@endforeach
</table>

<div class="break"></div>

<a href="" onclick="window.open('/forum/smileys', 'smileys', 'width=300, height=300, resizable=yes, scrollbars=yes, toolbar=no, location=no, directories=no, status=yes, menubar=no'); return false;">more</a>

</div>
