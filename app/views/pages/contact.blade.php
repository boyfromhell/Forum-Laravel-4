@extends('layout')

@section('content')

@include ('blocks.social')

<form class="form-horizontal unload-warning" method="post" action="/contact">
<div class="welcome wide no-margin">

	<div class="header">Contact</div>
	
	<div class="body">
	
		Contact me about registration issues, advertising information, and any other feedback<br><br>

	<div class="form-group">	
		<label class="col-sm-3 control-label">Your Name *</label>
		<div class="col-sm-5">
			{{ Form::text('name', $me->name, ['class' => 'form-control']) }}
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label">Your Email *</label>
		<div class="col-sm-5">
			{{ Form::text('email', $me->email, ['class' => 'form-control']) }}
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label">To</label>
		<div class="col-sm-5">
			<p class="form-control-static">Forum Admin</p>
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label">Anti-spam: {{{ Config::get('app.captcha_question') }}}?</label>
		<div class="col-sm-5">
			{{ Form::text('no_spam', '', ['class' => 'form-control']) }}
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label">Subject *</label>
		<div class="col-sm-5">
			{{ Form::text('subject', '', ['class' => 'form-control']) }}
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label">Message *</label>
		<div class="col-sm-5">
			{{ Form::textarea('message', '', ['id' => 'bbtext', 'class' => 'form-control']) }}
		</div>
	</div>

	<div class="text-center">
		<input class="btn btn-primary" name="send" type="submit" accesskey="S" value="Send Message">
	</div>

</div>
</div>
</form>

@stop
