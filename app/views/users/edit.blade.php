@extends('layout')

@section('content')

<a href="/profile" class="button">View Profile</a>

<div class="break"></div>

<form class="form2 unload-warning" method="post" action="/users/edit">
<div class="welcome">

	<div class="header">Account</div>
	
	<div class="body">
		<label class="left">Username</label>
		<span class="left"><b>{{{ $me->name }}}</b></span>
		
		<div class="break"></div>

		<label class="left">E-mail</label>
		<span id="email_text" class="left"><b>{{{ $me->email }}}</b> <a href="" onClick="$('#email_text').hide(); $('#email_input').show(); $('#email_input').focus(); $('#password_text').hide(); $('#password_input').show(); return false">change</a></span>
		<input class="left" id="email_input" tabindex="1" type="text" name="email" maxlength="255" style="display:none" value="{{{ $me->email }}}">
		
		<div class="break"></div>

		<label class="left">Current Password</label>
		<span class="left" id="password_text"><b>********</b></span>
		<input class="left" id="password_input" tabindex="1" type="password" name="old_password" style="display:none">
		<span class="left"><a id="password_link" href="" onClick="$('#password_text').hide(); $('#password_link').hide(); $('#password_input').show(); $('#password_input').focus(); $('#new_password').show(); $('#confirm_password').show(); return false">change</a></span>
		
		<div class="break"></div>
	
		<div id="new_password" style="display:none">
			<label class="left">New Password</label>
			<input class="left" tabindex="1" type="password" name="password">
			<div class="break"></div>
		</div>

		<div id="confirm_password" style="display:none">
			<label class="left">Confirm Password</label>
			<input class="left" tabindex="1" type="password" name="confirm">
			<div class="break"></div>
		</div>

	</div>

</div>

<div class="welcome">

	<div class="header">Personal</div>

	<div class="body">
		<label class="left">Birthday</label>
		{{ Form::select('year', $years, $year, ['class' => 'left', 'tabindex' => 1, 'style' => 'width:100px']) }}

		{{ Form::select('month', $months, $month, ['class' => 'left', 'tabindex' => 1, 'style' => 'width:125px']) }}

		{{ Form::select('day', $days, $day, ['class' => 'left', 'tabindex' => 1, 'style' => 'width:75px']) }}
		
		<div class="break"></div>

		<label class="left">Show in profile</label>
		<select class="left" name="bdaypref" tabindex="1" style="width:175px">
			<option value="0"{{ $me->bdaypref == 0 ? ' selected' : '' }}>Full birthday</option>
			<option value="1"{{ $me->bdaypref == 1 ? ' selected' : '' }}>Month and Day</option>
			<option value="2"{{ $me->bdaypref == 2 ? ' selected' : '' }}>Nothing</option>
		</select>

		<div class="break"></div>
	</div>
	
</div>

<div class="welcome">

	<div class="header">Contact</div>

	<div class="body">
		<label class="left">Website</label>
		<input class="left" tabindex="1" type="text" name="website" maxlength="255" value="{{{ $me->website }}}">

		<div class="break"></div>
	</div>

</div>

<div class="welcome no-margin">

	<div class="header">Profile</div>
	
	<div class="body">
		@foreach ( $customs as $field )
		<label class="left">{{{ $field->name }}}</label>
		<input class="left" tabindex="1" type="text" name="custom{{ $field->id }}"{{ $field->maxlength > 0 ? ' maxlength="'.$field->maxlength.'"' : '' }} value="{{{ $field->value }}}">
		
		<div class="break"></div>
		@endforeach

		<div style="margin:20px 0 30px 0">
		<label class="left">Signature</label>
		<div style="float:left">
			{{ BBCode::show_bbcode_controls() }}<br>
			<textarea id="bbtext" tabindex="1" name="sig">{{ BBCode::undo_prepare($me->sig) }}</textarea><br>
			<small>512 character limit</small>
		</div>

		<div class="break"></div>
		</div>

		<center>
	
		<input class="primary" tabindex="1" name="update" type="submit" value="Save Profile">
		<input type="reset" value="Reset">

		<div class="break"></div>
		
		</center>
	</div>
</div>
</form>

<a href="/profile" class="button">View Profile</a>

<div class="break"></div>

@stop
