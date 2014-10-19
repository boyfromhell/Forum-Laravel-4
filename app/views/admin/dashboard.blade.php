@extends('layout')

@section('content')

<div class="panel panel-primary">

	<div class="panel-heading">Admin</div>

	<div class="panel-body">

		<form method="post" action="/admin/reset-counters">
		<div>
			{{ Form::submit('Reset Counters', ['class' => 'btn btn-success btn-lg']) }}
		</div>
		</form>

	</div>

</div>

@stop
