@extends('emails.layout')

@section('content')

<p>
You have a new private message on {{ Config::get('app.forum_name') }}
</p>

<p>
Read and reply to this message:
</p>

<p>
<a href="{{ Config::get('app.url') }}{{ $message->url }}">{{ Config::get('app.url') }}{{ $message->url }}</a>
</p>

<p>
From: <a href="{{ Config::get('app.url') }}{{ $user->url }}">{{{ $user->name }}}</a><br>
Subject: <b>{{{ $thread->title }}}</b><br>
Message:
</p>

@if ( $attachment_count > 0 )
<p>
({{ $attachment_count }} attachment{{ $attachment_count != 1 ? 's' : '' }})
</p>
@endif

<p>
{{ BBCode::parse(BBCode::strip_quotes($message->content)) }}
</p>

@stop

