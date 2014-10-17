@extends('minimal')

@section('content')

<body class="shoutbox">

<div class="container-fluid">
<div class="row">
<div class="col-sm-3 text-center">

	<div class="panel panel-info">
	
		<div class="panel-heading">Shoutbox</div>
		
		<div class="panel-body">

    <form id="shoutbox" method="POST" onsubmit="saveData(); return false;">
	<div class="input-group">
		{{ Form::text('message', '', ['class' => 'form-control input-sm', 'placeholder' => 'type message here', 'autocomplete' => 'off']) }}
		<div class="input-group-btn">
		<input type="submit" class="btn btn-primary btn-sm" name="submit" value="Send">
		</div>
	</div>
    </form>

	</div></div>
	
	<small><a href="/community/shoutlog" target="_top" style="text-decoration:none">History</a> - <a id="sb_toggle" href="" onClick="toggleShoutBox(); return false" style="text-decoration:none">Disable</a></small>
	
</div>

<div class="col-sm-9">

	<table class="table table-condensed" data-last-id="{{ $last_id }}" data-last-time="{{ $last_time }}">
	<tbody>
	@foreach ( $shouts as $shout )
	
		@include ('shoutbox.row')
	
	@endforeach
	</tbody>
	</table>

</div>
</div>
</div>

</body>

@stop
