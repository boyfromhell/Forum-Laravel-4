@extends('layout')

@section('content')

<div class="panel panel-primary">

	<div class="panel-heading">I forgot my password</div>

	<div class="panel-body">

<form class="form-horizontal" action="{{ action('RemindersController@postRemind') }}" method="POST">
<div class="col-md-4">

<div class="form-group">
	<input type="email" name="email" class="form-control" placeholder="Username / Email">
</div>

<div class="form-group">
	<input type="submit" value="Reset my password" class="btn btn-default">
</div>

</div>
</form>

	</div>
</div>

@stop

