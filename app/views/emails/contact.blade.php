@extends('emails.layout')

@section('content')

<h2>Contact Form</h2>

<p>
The contact form was filled out.
</p>

<p>
From: <b>
@if ( $user_id )
<a href="{{ Config::get('app.url') }}{{ $user_url }}">{{{ $user_name }}}</a>
@else
{{{ $name }}} &lt;{{{ $email }}}&gt;
@endif
</b>
</p>

<p>
Subject: <b>{{{ $subject }}}</b>
</p>

<p>
Message:
</p>

<p>
{{ BBCode::parse($message) }}
</p>

@stop
