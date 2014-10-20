@extends('layout')

@section('content')

@include ('blocks.social')

<form class="form-horizontal unload-warning" method="post" action="/contact">
<div class="panel panel-primary">

	<div class="panel-heading">Contact</div>
	
	<div class="panel-body">
	
		Contact me about registration issues, advertising information, and any other feedback<br><br>

	<div class="form-group">	
		<label class="col-sm-4 control-label">Your Name *</label>
		<div class="col-sm-5">
			{{ Form::text('name', $me->name, ['class' => 'form-control', 'required']) }}
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-4 control-label">Your Email *</label>
		<div class="col-sm-5">
			{{ Form::text('email', $me->email, ['class' => 'form-control', 'required']) }}
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-4 control-label">To</label>
		<div class="col-sm-5">
			<p class="form-control-static">Forum Admin</p>
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-4 control-label">Subject *</label>
		<div class="col-sm-5">
			{{ Form::text('subject', '', ['class' => 'form-control', 'required']) }}
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-4 control-label">Message *</label>
		<div class="col-sm-5">
			{{ Form::textarea('message', '', ['id' => 'bbtext', 'class' => 'form-control', 'required']) }}
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-4 control-label">Anti-spam *</label>
		<div class="col-sm-5">
			{{ Form::captcha() }}
		</div>
	</div>

</div>

<div class="panel-footer">

	<div class="form-group">
		<div class="col-sm-5 col-sm-offset-4">
			{{ Form::submit('Send Message', ['class' => 'btn btn-primary btn-once', 'data-loading-text' => 'Sending...']) }}
		</div>
	</div>

</div>
</div>
</form>

@stop
