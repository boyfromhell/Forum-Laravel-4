@extends('minimal')

@section('content')

<body class="shoutbox">

<div class="row">
<div class="col-sm-3 text-center">

	<div class="welcome small" style="margin-bottom:3px">
	
		<div class="header">Shoutbox</div>
		
		<div class="body">

    <form id="shoutbox" method="POST" onsubmit="saveData(); return false;">
		{{ Form::text('message', '', ['class' => 'form-control input-sm', 'placeholder' => 'type message here', 'autocomplete' => 'off']) }}
		<br>
		<input type="submit" class="btn btn-primary btn-sm" name="submit" value="Send">
    </form>

	</div></div>
	
	<small><a href="/community/shoutlog" target="_top" style="text-decoration:none">History</a> - <a id="sb_toggle" href="" onClick="toggleShoutBox(); return false" style="text-decoration:none">Disable</a></small>
	
</div>

<div class="col-sm-9">

	<table class="shouts" cellpadding="0" cellspacing="0" border="0" width="100%" data-last-id="{{ $last_id }}" data-last-time="{{ $last_time }}">
	@foreach ( $shouts as $shout )
	
		@include ('shoutbox.row')
	
	@endforeach
	</table>

</div>

</div>

</body>

@stop
