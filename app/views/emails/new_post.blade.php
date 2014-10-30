@extends('emails.layout')

@section('content')

<p>
Reply to topic <b>{{{ $topic->title }}}</b> on {{ Config::get('app.forum_name') }}<br>
There may be other replies, but you will not receive another notification until you view the topic.
</p>

<p>
Manage your topic subscriptions:<br>
<a href="{{ Config::get('app.url') }}/users/topics">{{ Config::get('app.url') }}/users/topics</a>
</p>

<p>
View this post:<br>
<a href="{{ Config::get('app.url') }}{{ $post->url }}">{{ Config::get('app.url') }}{{ $post->url }}</a>
</p>

<p>
Posted by <a href="{{ Config::get('app.url') }}{{ $user->url }}">{{{ $user->name }}}</a>, {{ Helpers::date_string($post->created_at, 2) }}
</p>

<p>
{{ BBCode::parse(BBCode::strip_quotes($content)) }}
</p>

@stop
