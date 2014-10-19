@extends('emails.layout')

@section('content')

<h2>Password Reset</h2>

<p>
To reset your password, complete this form: {{ URL::to('reset-password', array($token)) }}.
</p>

<p>
This link will expire in {{ Config::get('auth.reminder.expire', 60) }} minutes.
</p>

@stop
