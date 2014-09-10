@extends('minimal')

@section('content')

<body class="shoutbox">

<div style="width:20%; float:left;">

<div style="width:90%; text-align:center;">
	<div class="welcome small" style="margin-bottom:3px">
	
		<div class="header">Shoutbox</div>
		
		<div class="body">

    <form id="shoutbox" method="POST" onsubmit="saveData(); return false;">
		<input class="post" type="text" style="width:90%" name="message" maxlength="500" placeholder="type message here" autocomplete="off">
		<br>
		<input type="submit" name="submit" value="Send">
    </form>

	</div></div>
	
	<small><a href="/community/shoutlog" target="_top" style="text-decoration:none">History</a> - <a id="sb_toggle" href="" onClick="toggleShoutBox(); return false" style="text-decoration:none">Disable</a></small>
	
</div></div>

<div style="float:left; width:80%">

	<table class="shouts" cellpadding="0" cellspacing="0" border="0" width="100%" data-last-id="{{ $last_id }}" data-last-time="{{ $last_time }}">
	@foreach ( $shouts as $shout )
	
		@include ('shoutbox.row')
	
	@endforeach
	</table>

</div>

<div class="break"></div>

</body>

@stop