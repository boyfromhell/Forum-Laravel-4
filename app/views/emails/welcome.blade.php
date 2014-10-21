@extends('emails.layout')

@section('content')

<p>
Welcome to <a href="{{ Config::get('app.url') }}">{{{ Config::get('app.forum_name') }}}</a>
</p>

<p>
Please keep this email for your records. Your account information is as follows:
</p>

<hr>
<p>
Username: <b>{{{ $user_name }}}</b><br>
Password: <b>{{{ $unencrypted }}}</b>
</p>
<hr>

<p>
Please do not forget your password as it has been encrypted in our database and we cannot retrieve it for you. However, should you forget your password you can request a new one which will be activated in the same way as this account.
</p>

<p>
Thanks for registering! Feel welcome to introduce yourself to the rest of the forum.
</p>

- {{{ Config::get('app.forum_name') }}} team

@stop
