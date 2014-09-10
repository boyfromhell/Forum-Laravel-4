@extends('layout')

@section('content')

@include ('blocks.social')

<form class="form2 unload-warning wide" method="post" action="/contact">
<div class="welcome wide no-margin">

	<div class="header">Contact</div>
	
	<div class="body">
	
		Contact me about registration issues, advertising information, and any other feedback<br><br>
	
		<label class="left">Your Name</label>
		{{ Form::text('name', $me->name, ['class' => 'left', 'tabindex' => 1]) }}
		<div class="break"></div>
		
		<label class="left">Your Email</label>
		{{ Form::text('email', $me->email, ['class' => 'left', 'tabindex' => 1]) }}
		<div class="break"></div>

		<label class="left">To</label>
		<span class="left">Forum Admin</span>
		<div class="break"></div>
		
		<label class="left">Anti-spam: {{{ Config::get('app.captcha_question') }}}?</label>
		{{ Form::text('no_spam', '', ['class' => 'left', 'tabindex' => 1]) }}
		<div class="break"></div>
	
		<label class="left">Subject</label>
		{{ Form::text('subject', '', ['class' => 'left', 'tabindex' => 1]) }}
		<div class="break"></div>

		<label class="left">Message</label>
		<div class="float_left">
			{{ Form::textarea('message', '', ['id' => 'bbtext', 'tabindex' => 1]) }}
		</div>
		
		<div class="break"></div>
		
		<center>
	
		<input class="primary" tabindex="1" name="send" type="submit" accesskey="S" value="Send Message">

		<div class="break"></div>
		
		</center>
	</div>
</div>
</form>

@stop
